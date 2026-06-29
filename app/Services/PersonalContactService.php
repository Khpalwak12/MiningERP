<?php

namespace App\Services;

use App\Contracts\Repositories\PersonalContactRepositoryInterface;
use App\Models\PersonalContact;
use App\Models\PersonalLedgerTransaction;
use App\Support\JalaliDate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PersonalContactService
{
    public function __construct(private PersonalContactRepositoryInterface $repository) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->withBalances($filters);
    }

    public function create(array $data): PersonalContact
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(PersonalContact $contact, array $data): PersonalContact
    {
        return DB::transaction(fn () => $this->repository->update($contact, $data));
    }

    public function delete(PersonalContact $contact): bool
    {
        return DB::transaction(fn () => $this->repository->delete($contact));
    }

    public function balances(PersonalContact $contact): array
    {
        return [
            PersonalLedgerTransaction::CURRENCY_AFN => $contact->balanceForCurrency(PersonalLedgerTransaction::CURRENCY_AFN),
            PersonalLedgerTransaction::CURRENCY_USD => $contact->balanceForCurrency(PersonalLedgerTransaction::CURRENCY_USD),
        ];
    }

    public function ledger(PersonalContact $contact, ?string $currency = null): Collection
    {
        $query = $contact->ledgerTransactions()->orderBy('transaction_date')->orderBy('id');

        if ($currency) {
            $query->where('currency', $currency);
        }

        $running = ['AFN' => 0.0, 'USD' => 0.0];

        return $query->get()->map(function (PersonalLedgerTransaction $row) use (&$running) {
            $currency = $row->currency;
            $amount = (float) $row->amount;
            $delta = $row->isCredit() ? $amount : -$amount;
            $running[$currency] = round(($running[$currency] ?? 0) + $delta, 2);

            return [
                'id' => $row->id,
                'date' => $row->transaction_date?->format('Y-m-d'),
                'date_shamsi' => JalaliDate::fromGregorian($row->transaction_date),
                'type' => $row->transaction_type,
                'currency' => $currency,
                'amount' => $amount,
                'credit' => $row->isCredit() ? $amount : null,
                'payment' => $row->isCredit() ? null : $amount,
                'description' => $row->description,
                'running_balance' => $running[$currency],
                'is_locked' => $row->isInClosedFinancialYear(),
            ];
        })->sortByDesc('date')->values();
    }
}

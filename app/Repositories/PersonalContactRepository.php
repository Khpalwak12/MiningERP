<?php

namespace App\Repositories;

use App\Contracts\Repositories\PersonalContactRepositoryInterface;
use App\Models\PersonalContact;
use App\Models\PersonalLedgerTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PersonalContactRepository extends BaseRepository implements PersonalContactRepositoryInterface
{
    public function __construct(PersonalContact $model)
    {
        parent::__construct($model);
    }

    public function withBalances(array $filters = []): LengthAwarePaginator
    {
        $query = $this->applyFilters($this->model->newQuery(), $filters);

        foreach ([
            PersonalLedgerTransaction::CURRENCY_AFN => ['credit_afn', 'payment_afn'],
            PersonalLedgerTransaction::CURRENCY_USD => ['credit_usd', 'payment_usd'],
        ] as $currency => [$creditAlias, $paymentAlias]) {
            $query->withSum([
                "ledgerTransactions as {$creditAlias}" => fn ($q) => $q
                    ->where('currency', $currency)
                    ->where('transaction_type', PersonalLedgerTransaction::TYPE_CREDIT),
            ], 'amount');

            $query->withSum([
                "ledgerTransactions as {$paymentAlias}" => fn ($q) => $q
                    ->where('currency', $currency)
                    ->where('transaction_type', PersonalLedgerTransaction::TYPE_PAYMENT),
            ], 'amount');
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['contact_type'])) {
            $query->where('contact_type', $filters['contact_type']);
        }

        return $query;
    }
}

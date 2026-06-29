<?php

namespace App\Services;

use App\Models\PersonalContact;
use App\Models\PersonalLedgerTransaction;
use App\Support\ActiveFinancialYear;
use App\Support\JalaliDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PersonalAccountsReportService
{
    public function __construct(private ReportAdvancedFilterService $advancedFilter) {}

    public function ledgerSummary(array $filters = []): array
    {
        $summary = [];

        foreach ([PersonalLedgerTransaction::CURRENCY_AFN, PersonalLedgerTransaction::CURRENCY_USD] as $currency) {
            if (! empty($filters['currency']) && $filters['currency'] !== $currency) {
                continue;
            }

            $base = $this->ledgerQuery($filters)->where('currency', $currency);
            $credit = (float) (clone $base)->where('transaction_type', PersonalLedgerTransaction::TYPE_CREDIT)->sum('amount');
            $payment = (float) (clone $base)->where('transaction_type', PersonalLedgerTransaction::TYPE_PAYMENT)->sum('amount');
            $key = strtolower($currency);

            $summary["total_credit_{$key}"] = $credit;
            $summary["total_payment_{$key}"] = $payment;
            $summary["outstanding_{$key}"] = round($credit - $payment, 2);
        }

        return $summary;
    }

    public function ledgerTransactions(array $filters = []): Collection
    {
        $running = [];

        return $this->ledgerQuery($filters)
            ->with('contact')
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get()
            ->map(function (PersonalLedgerTransaction $row) use (&$running) {
                $key = $row->personal_contact_id.'-'.$row->currency;
                $amount = (float) $row->amount;
                $delta = $row->isCredit() ? $amount : -$amount;
                $running[$key] = round(($running[$key] ?? 0) + $delta, 2);

                return [
                    'id' => $row->id,
                    'contact_name' => $row->contact?->name,
                    'date_shamsi' => JalaliDate::fromGregorian($row->transaction_date),
                    'type' => $row->transaction_type,
                    'currency' => $row->currency,
                    'credit_amount' => $row->isCredit() ? $amount : null,
                    'payment_amount' => $row->isCredit() ? null : $amount,
                    'description' => $row->description,
                    'running_balance' => $running[$key],
                ];
            });
    }

    public function contactBalances(array $filters = []): Collection
    {
        $query = PersonalContact::query()->orderBy('name');

        if (! empty($filters['personal_contact_id'])) {
            $query->where('id', $filters['personal_contact_id']);
        }

        $fyId = ActiveFinancialYear::activeYearId();
        $allYears = ActiveFinancialYear::isAllYearsMode();

        foreach ([
            PersonalLedgerTransaction::CURRENCY_AFN => ['credit_afn', 'payment_afn'],
            PersonalLedgerTransaction::CURRENCY_USD => ['credit_usd', 'payment_usd'],
        ] as $currency => [$creditAlias, $paymentAlias]) {
            $query->withSum([
                "ledgerTransactions as {$creditAlias}" => fn ($q) => $this->scopedLedgerSum($q, $currency, PersonalLedgerTransaction::TYPE_CREDIT, $fyId, $allYears),
            ], 'amount');

            $query->withSum([
                "ledgerTransactions as {$paymentAlias}" => fn ($q) => $this->scopedLedgerSum($q, $currency, PersonalLedgerTransaction::TYPE_PAYMENT, $fyId, $allYears),
            ], 'amount');
        }

        return $query->get()->map(fn (PersonalContact $contact) => [
            'id' => $contact->id,
            'name' => $contact->name,
            'contact_type' => $contact->contact_type,
            'balance_afn' => round((float) ($contact->credit_afn ?? 0) - (float) ($contact->payment_afn ?? 0), 2),
            'balance_usd' => round((float) ($contact->credit_usd ?? 0) - (float) ($contact->payment_usd ?? 0), 2),
        ]);
    }

    private function ledgerQuery(array $filters): Builder
    {
        $query = PersonalLedgerTransaction::query();
        $this->applyFinancialYearFilter($query);
        $this->applyDateFilters($query, $filters, 'transaction_date');

        if (! empty($filters['personal_contact_id'])) {
            $query->where('personal_contact_id', $filters['personal_contact_id']);
        }

        if (! empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        $this->advancedFilter->apply($query, 'personal-ledger', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query;
    }

    private function scopedLedgerSum(Builder $query, string $currency, string $type, ?int $fyId, bool $allYears): void
    {
        $query->where('currency', $currency)->where('transaction_type', $type);

        if (! $allYears && $fyId) {
            $query->where('financial_year_id', $fyId);
        }
    }

    private function applyFinancialYearFilter(Builder $query): void
    {
        if (! ActiveFinancialYear::isAllYearsMode()) {
            $activeId = ActiveFinancialYear::activeYearId();

            if ($activeId) {
                $query->where('financial_year_id', $activeId);
            }
        }
    }

    private function applyDateFilters(Builder $query, array $filters, string $column): void
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate($column, '>=', JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate($column, '<=', JalaliDate::toGregorian($filters['date_to']));
        }
    }
}

<?php

namespace App\Services;

use App\Models\ContractorPayment;
use App\Models\ContractorProduction;
use App\Models\ContractorSalaryCharge;
use App\Support\ActiveFinancialYear;
use App\Support\JalaliDate;
use Illuminate\Support\Collection;

class ContractorRoyaltyLedgerService
{
    public function summary(array $filters = []): array
    {
        $productionQuery = ContractorProduction::query();
        $paymentQuery = ContractorPayment::query();
        $salaryChargeQuery = ContractorSalaryCharge::query();

        $this->applyFinancialYearFilter($productionQuery);
        $this->applyFinancialYearFilter($paymentQuery);
        $this->applyFinancialYearFilter($salaryChargeQuery);
        $this->applyDateFilters($productionQuery, $filters, 'production_date');
        $this->applyDateFilters($paymentQuery, $filters, 'payment_date');
        $this->applyDateFilters($salaryChargeQuery, $filters, 'charge_date');

        $totalRoyalties = (float) (clone $productionQuery)->sum('total_royalty');
        $totalSalaryCharges = (float) (clone $salaryChargeQuery)->sum('amount');
        $totalPayments = (float) (clone $paymentQuery)->sum('amount');
        $totalReceivable = $totalRoyalties + $totalSalaryCharges;

        return [
            'total_royalties' => $totalRoyalties,
            'dispatch_count' => (clone $productionQuery)->count(),
            'total_tons' => (float) (clone $productionQuery)->sum('quantity_ton'),
            'total_salary_charges' => $totalSalaryCharges,
            'total_receivable' => $totalReceivable,
            'total_payments' => $totalPayments,
            'outstanding_balance' => $totalReceivable - $totalPayments,
        ];
    }

    public function transactions(array $filters = []): Collection
    {
        $productionQuery = ContractorProduction::query();
        $paymentQuery = ContractorPayment::query();
        $salaryChargeQuery = ContractorSalaryCharge::query()->with('employee');

        $this->applyFinancialYearFilter($productionQuery);
        $this->applyFinancialYearFilter($paymentQuery);
        $this->applyFinancialYearFilter($salaryChargeQuery);
        $this->applyDateFilters($productionQuery, $filters, 'production_date');
        $this->applyDateFilters($paymentQuery, $filters, 'payment_date');
        $this->applyDateFilters($salaryChargeQuery, $filters, 'charge_date');

        $productions = $productionQuery->orderBy('production_date')->get()->map(fn (ContractorProduction $row) => [
            'id' => 'production-'.$row->id,
            'type' => 'royalty',
            'date' => $row->production_date?->format('Y-m-d'),
            'date_shamsi' => JalaliDate::fromGregorian($row->production_date),
            'description' => trim($row->remarks ?? __('erp.contractor_royalty.production_entry')),
            'quantity_ton' => (float) $row->quantity_ton,
            'rate_per_ton' => (float) $row->rate_per_ton,
            'royalty_amount' => (float) $row->total_royalty,
            'payment_amount' => null,
        ]);

        $salaryCharges = $salaryChargeQuery->orderBy('charge_date')->get()->map(fn (ContractorSalaryCharge $row) => [
            'id' => 'salary-charge-'.$row->id,
            'type' => 'salary_charge',
            'date' => $row->charge_date?->format('Y-m-d'),
            'date_shamsi' => JalaliDate::fromGregorian($row->charge_date),
            'description' => trim($row->remarks ?? __('erp.contractor_royalty.salary_charge_entry')),
            'quantity_ton' => null,
            'rate_per_ton' => null,
            'royalty_amount' => (float) $row->amount,
            'payment_amount' => null,
        ]);

        $payments = $paymentQuery->orderBy('payment_date')->get()->map(fn (ContractorPayment $row) => [
            'id' => 'payment-'.$row->id,
            'type' => 'payment',
            'date' => $row->payment_date?->format('Y-m-d'),
            'date_shamsi' => JalaliDate::fromGregorian($row->payment_date),
            'description' => trim(($row->receipt_number ? $row->receipt_number.' — ' : '').($row->received_by ?? '').($row->remarks ? ' — '.$row->remarks : '')),
            'quantity_ton' => null,
            'rate_per_ton' => null,
            'royalty_amount' => null,
            'payment_amount' => (float) $row->amount,
        ]);

        return $productions->concat($salaryCharges)->concat($payments)
            ->sortBy([
                ['date', 'asc'],
                ['type', 'asc'],
            ])
            ->values();
    }

    private function applyFinancialYearFilter($query): void
    {
        if (! ActiveFinancialYear::isAllYearsMode()) {
            $activeId = ActiveFinancialYear::activeYearId();

            if ($activeId) {
                $query->where('financial_year_id', $activeId);
            }
        }
    }

    private function applyDateFilters($query, array $filters, string $column): void
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate($column, '>=', JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate($column, '<=', JalaliDate::toGregorian($filters['date_to']));
        }
    }
}

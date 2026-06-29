<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class ReportAdvancedFilterService
{
    public function apply(Builder $query, string $reportType, ?string $filterBy, mixed $filterValue): void
    {
        if (empty($filterBy) || $filterValue === null || $filterValue === '') {
            return;
        }

        match ($reportType) {
            'sales' => $this->applySales($query, $filterBy, $filterValue),
            'payments' => $this->applyPayments($query, $filterBy, $filterValue),
            'expenses' => $this->applyExpenses($query, $filterBy, $filterValue),
            'employees' => $this->applyEmployees($query, $filterBy, $filterValue),
            'payroll' => $this->applyPayroll($query, $filterBy, $filterValue),
            'inventory' => $this->applyInventory($query, $filterBy, $filterValue),
            'daily-production' => $this->applyDailyProduction($query, $filterBy, $filterValue),
            'mine-assets' => $this->applyMineAssets($query, $filterBy, $filterValue),
            'personal-ledger' => $this->applyPersonalLedger($query, $filterBy, $filterValue),
            'personal-home-expenses' => $this->applyPersonalHomeExpenses($query, $filterBy, $filterValue),
            'machinery' => $this->applyMachinery($query, $filterBy, $filterValue),
            default => null,
        };
    }

    private function applySales(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'shipment_number' => is_numeric($value)
                ? $query->where('id', (int) $value)
                : $this->like($query, 'id', $value),
            'quantity' => $this->exactNumeric($query, 'quantity_ton', $value),
            'price_per_ton' => $this->exactNumeric($query, 'price_per_ton', $value),
            'total_amount' => $this->exactNumeric($query, 'total_amount', $value),
            'created_by' => $this->creatorNameLike($query, $value),
            'notes' => $this->like($query, 'notes', $value),
            default => null,
        };
    }

    private function applyPayments(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'customer' => $query->where('customer_id', (int) $value),
            'receipt_number' => $this->like($query, 'receipt_number', $value),
            'received_by' => $this->like($query, 'received_by', $value),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'notes' => $this->like($query, 'notes', $value),
            default => null,
        };
    }

    private function applyExpenses(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'category' => $query->where('expense_category_id', (int) $value),
            'sub_category' => $this->like($query, 'subcategory', $value),
            'bill_number' => $this->like($query, 'bill_number', $value),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'description' => $this->like($query, 'description', $value),
            default => null,
        };
    }

    private function applyEmployees(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'employee_name' => $this->like($query, 'name', $value),
            'father_name' => $this->like($query, 'father_name', $value),
            'position' => $this->like($query, 'position', $value),
            'phone' => $this->like($query, 'phone', $value),
            'status' => $query->where('status', $value),
            'salary' => $this->exactNumeric($query, 'salary', $value),
            default => null,
        };
    }

    private function applyPayroll(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'employee' => $query->where('employee_id', (int) $value),
            'receipt_number' => $this->like($query, 'period_month', $value),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'payment_type' => $query->where('payment_type', $value),
            'paid_by' => $this->creatorNameLike($query, $value),
            'notes' => $this->like($query, 'notes', $value),
            default => null,
        };
    }

    private function applyInventory(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'item_name' => $query->whereHas('inventoryItem', fn (Builder $q) => $this->like($q, 'name', $value)),
            'item_code' => $query->whereHas('inventoryItem', fn (Builder $q) => $this->like($q, 'sku', $value)),
            'category' => $query->whereHas('inventoryItem', fn (Builder $q) => $this->like($q, 'category', $value)),
            'quantity' => $this->exactNumeric($query, 'quantity', $value),
            'movement_type' => $query->where('movement_type', $value),
            default => null,
        };
    }

    private function applyDailyProduction(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'quantity' => $this->exactNumeric($query, 'quantity_ton', $value),
            'total_tons' => $this->exactNumeric($query, 'quantity_ton', $value),
            'created_by' => $this->creatorNameLike($query, $value),
            'notes' => $this->like($query, 'notes', $value),
            default => null,
        };
    }

    private function applyMineAssets(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'name' => $this->like($query, 'name', $value),
            'related_to' => $this->like($query, 'related_to', $value),
            'quantity' => $this->exactNumeric($query, 'quantity', $value),
            'unit' => $query->where('unit', $value),
            'status' => $query->where('status', $value),
            'remarks' => $this->like($query, 'remarks', $value),
            default => null,
        };
    }

    private function applyPersonalLedger(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'contact_name' => $query->whereHas('contact', fn (Builder $q) => $this->like($q, 'name', $value)),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'description' => $this->like($query, 'description', $value),
            'transaction_type' => $query->where('transaction_type', $value),
            default => null,
        };
    }

    private function applyPersonalHomeExpenses(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'item_name' => $this->like($query, 'item_name', $value),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'description' => $this->like($query, 'description', $value),
            default => null,
        };
    }

    private function applyMachinery(Builder $query, string $filterBy, mixed $value): void
    {
        match ($filterBy) {
            'item_name' => $this->like($query, 'item_name', $value),
            'bill_number' => $this->like($query, 'bill_number', $value),
            'amount' => $this->exactNumeric($query, 'amount', $value),
            'description' => $this->like($query, 'description', $value),
            'currency' => $query->where('currency', $value),
            default => null,
        };
    }

    private function like(Builder $query, string $column, mixed $value): void
    {
        $term = addcslashes(trim((string) $value), '%_\\');
        $query->where($column, 'like', '%'.$term.'%');
    }

    private function exactNumeric(Builder $query, string $column, mixed $value): void
    {
        $query->where($column, (float) $value);
    }

    private function creatorNameLike(Builder $query, mixed $value): void
    {
        $term = addcslashes(trim((string) $value), '%_\\');
        $query->whereHas('creator', fn (Builder $q) => $q->where('name', 'like', '%'.$term.'%'));
    }
}

<?php

namespace App\Services;

use App\Models\ContractorExpenseCharge;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ContractorExpenseChargeService
{
    public function syncFromExpense(Expense $expense): void
    {
        $expense->loadMissing('category');

        if (! $expense->is_for_contractor) {
            ContractorExpenseCharge::query()
                ->where('expense_id', $expense->id)
                ->delete();

            return;
        }

        $amount = (float) $expense->amount;

        if ($amount <= 0) {
            ContractorExpenseCharge::query()
                ->where('expense_id', $expense->id)
                ->delete();

            return;
        }

        $categoryName = $expense->category?->name ?? '';
        $description = trim(collect([
            $categoryName,
            $expense->subcategory,
            $expense->description,
        ])->filter()->implode(' — '));

        ContractorExpenseCharge::query()->updateOrCreate(
            ['expense_id' => $expense->id],
            [
                'financial_year_id' => $expense->financial_year_id,
                'charge_date' => $expense->expense_date,
                'amount' => $amount,
                'remarks' => $description !== ''
                    ? __('erp.contractor_royalty.expense_charge_for_expense', ['description' => $description])
                    : __('erp.contractor_royalty.expense_charge_entry'),
                'created_by' => Auth::id(),
            ]
        );
    }

    public function removeForExpense(Expense $expense): void
    {
        ContractorExpenseCharge::query()
            ->where('expense_id', $expense->id)
            ->delete();
    }
}

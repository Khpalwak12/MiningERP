<?php

namespace App\Services;

use App\Models\ContractorSalaryCharge;
use App\Models\Employee;
use App\Models\PayrollPayment;
use Illuminate\Support\Facades\Auth;

class ContractorSalaryChargeService
{
    public function syncFromPayrollPayment(PayrollPayment $payment): void
    {
        $payment->loadMissing('employee');
        $employee = $payment->employee;

        if (! $employee instanceof Employee || ! $employee->is_shared_with_contractor) {
            ContractorSalaryCharge::query()
                ->where('payroll_payment_id', $payment->id)
                ->delete();

            return;
        }

        $amount = $employee->contractorSalaryShareForAmount((float) $payment->amount);

        if ($amount <= 0) {
            ContractorSalaryCharge::query()
                ->where('payroll_payment_id', $payment->id)
                ->delete();

            return;
        }

        $description = __('erp.contractor_royalty.salary_charge_for_employee', [
            'employee' => $employee->name,
            'period' => $payment->period_month ?? '',
        ]);

        ContractorSalaryCharge::query()->updateOrCreate(
            ['payroll_payment_id' => $payment->id],
            [
                'financial_year_id' => $payment->financial_year_id,
                'employee_id' => $employee->id,
                'charge_date' => $payment->payment_date,
                'period_month' => $payment->period_month,
                'amount' => $amount,
                'remarks' => trim($description),
                'created_by' => Auth::id(),
            ]
        );
    }

    public function removeForPayrollPayment(PayrollPayment $payment): void
    {
        ContractorSalaryCharge::query()
            ->where('payroll_payment_id', $payment->id)
            ->delete();
    }
}

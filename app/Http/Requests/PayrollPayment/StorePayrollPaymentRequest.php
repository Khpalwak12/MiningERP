<?php

namespace App\Http\Requests\PayrollPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('payroll.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['payment_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['payment_date']), [
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', Rule::in(['full_salary', 'advance', 'partial'])],
            'period_month' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

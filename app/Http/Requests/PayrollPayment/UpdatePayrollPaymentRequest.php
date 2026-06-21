<?php

namespace App\Http\Requests\PayrollPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePayrollPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('payroll.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('payment_date')) {
            $this->convertShamsiDates(['payment_date']);
        }
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'required', 'exists:employees,id'],
            'payment_date' => ['sometimes', 'required', 'date'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'payment_type' => ['sometimes', 'required', Rule::in(['full_salary', 'advance', 'partial'])],
            'period_month' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

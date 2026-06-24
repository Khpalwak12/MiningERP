<?php

namespace App\Http\Requests\PayrollPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\NormalizesPayrollPeriodMonth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePayrollPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use NormalizesPayrollPeriodMonth;

    public function authorize(): bool
    {
        return $this->user()?->can('payroll.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('payment_date')) {
            $this->convertShamsiDates(['payment_date']);
        }

        $this->normalizePayrollPeriodMonth();
    }

    public function rules(): array
    {
        return array_merge($this->payrollPeriodMonthRules(), [
            'employee_id' => ['sometimes', 'required', 'exists:employees,id'],
            'payment_date' => ['sometimes', 'required', 'date'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'payment_type' => ['sometimes', 'required', Rule::in(['full_salary', 'advance', 'partial'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

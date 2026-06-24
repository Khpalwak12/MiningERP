<?php

namespace App\Http\Requests\PayrollPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\NormalizesPayrollPeriodMonth;
use App\Http\Requests\Concerns\ValidatesActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrollPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use NormalizesPayrollPeriodMonth;
    use ValidatesActiveFinancialYear;

    public function authorize(): bool
    {
        return $this->user()?->can('payroll.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['payment_date']);
        $this->normalizePayrollPeriodMonth();
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['payment_date']), $this->payrollPeriodMonthRules(), [
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', Rule::in(['full_salary', 'advance', 'partial'])],
            'notes' => ['nullable', 'string'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $this->ensureWritableFinancialYear($validator);
    }
}

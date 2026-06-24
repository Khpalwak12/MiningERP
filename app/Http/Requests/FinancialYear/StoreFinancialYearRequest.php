<?php

namespace App\Http\Requests\FinancialYear;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Models\FinancialYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialYearRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('financial-year.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:10', 'unique:financial_years,name'],
            ...$this->shamsiDateRules(['start_date', 'end_date']),
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in([FinancialYear::STATUS_ACTIVE, FinancialYear::STATUS_CLOSED])],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['start_date', 'end_date']);
    }
}

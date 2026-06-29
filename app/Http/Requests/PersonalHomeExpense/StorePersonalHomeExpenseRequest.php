<?php

namespace App\Http\Requests\PersonalHomeExpense;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\ValidatesActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonalHomeExpenseRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use ValidatesActiveFinancialYear;

    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['expense_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['expense_date']), [
            'item_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $this->ensureWritableFinancialYear($validator);
    }
}

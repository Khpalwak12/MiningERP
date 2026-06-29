<?php

namespace App\Http\Requests\PersonalHomeExpense;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalHomeExpenseRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('expense_date')) {
            $this->convertShamsiDates(['expense_date']);
        }
    }

    public function rules(): array
    {
        return [
            'expense_date' => ['sometimes', 'required', 'date'],
            'item_name' => ['sometimes', 'required', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ];
    }
}

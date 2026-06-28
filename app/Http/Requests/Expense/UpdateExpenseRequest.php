<?php

namespace App\Http\Requests\Expense;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('expenses.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('expense_date')) {
            $this->convertShamsiDates(['expense_date']);
        }

        if ($this->has('bill_number') && $this->input('bill_number') === '') {
            $this->merge(['bill_number' => null]);
        }

        if ($this->has('is_for_contractor')) {
            $this->merge([
                'is_for_contractor' => filter_var($this->input('is_for_contractor'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['sometimes', 'required', 'exists:expense_categories,id'],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'max:255'],
            'expense_date' => ['sometimes', 'required', 'date'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'is_for_contractor' => ['sometimes', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ];
    }
}

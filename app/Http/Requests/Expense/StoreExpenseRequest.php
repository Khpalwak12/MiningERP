<?php

namespace App\Http\Requests\Expense;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('expenses.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['expense_date']);

        if ($this->has('bill_number') && $this->input('bill_number') === '') {
            $this->merge(['bill_number' => null]);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['expense_date']), [
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:5120'],
        ]);
    }
}

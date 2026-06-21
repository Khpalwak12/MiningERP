<?php

namespace App\Http\Requests\Accounting;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalEntryRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('accounting.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('entry_date')) {
            $this->convertShamsiDates(['entry_date']);
        }
    }

    public function rules(): array
    {
        return [
            'entry_date' => ['sometimes', 'required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'lines' => ['sometimes', 'required', 'array', 'min:2'],
            'lines.*.account_id' => ['required_with:lines', 'exists:accounts,id'],
            'lines.*.debit' => ['required_with:lines', 'numeric', 'min:0'],
            'lines.*.credit' => ['required_with:lines', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
        ];
    }
}

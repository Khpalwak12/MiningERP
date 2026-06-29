<?php

namespace App\Http\Requests\PersonalLedgerTransaction;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalLedgerTransactionRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('transaction_date')) {
            $this->convertShamsiDates(['transaction_date']);
        }
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['sometimes', 'required', 'date'],
            'transaction_type' => ['sometimes', 'required', Rule::in(['credit', 'payment'])],
            'currency' => ['sometimes', 'required', Rule::in(['AFN', 'USD'])],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ];
    }
}

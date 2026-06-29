<?php

namespace App\Http\Requests\PersonalLedgerTransaction;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\ValidatesActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonalLedgerTransactionRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use ValidatesActiveFinancialYear;

    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['transaction_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['transaction_date']), [
            'transaction_type' => ['required', Rule::in(['credit', 'payment'])],
            'currency' => ['required', Rule::in(['AFN', 'USD'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $this->ensureWritableFinancialYear($validator);
    }
}

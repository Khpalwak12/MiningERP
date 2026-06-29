<?php

namespace App\Http\Requests\Machinery;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\ValidatesActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMachineryItemRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use ValidatesActiveFinancialYear;

    public function authorize(): bool
    {
        return $this->user()?->can('machinery.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['purchase_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['purchase_date']), [
            'item_name' => ['required', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'max:255'],
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

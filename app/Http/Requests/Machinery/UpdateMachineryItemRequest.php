<?php

namespace App\Http\Requests\Machinery;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMachineryItemRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('machinery.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('purchase_date')) {
            $this->convertShamsiDates(['purchase_date']);
        }
    }

    public function rules(): array
    {
        return [
            'purchase_date' => ['sometimes', 'required', 'date'],
            'item_name' => ['sometimes', 'required', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'max:255'],
            'currency' => ['sometimes', 'required', Rule::in(['AFN', 'USD'])],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\SankariStoneSale;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSankariStoneSaleRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('sankari.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('sale_date')) {
            $this->convertShamsiDates(['sale_date']);
        }
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['sometimes', 'required', 'date'],
            'truck_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'price_per_truck' => ['sometimes', 'required', 'numeric', 'min:0'],
            'payment_type' => ['sometimes', 'required', Rule::in(['cash', 'credit'])],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

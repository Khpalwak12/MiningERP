<?php

namespace App\Http\Requests\SankariStoneSale;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\SankariStoneSale\Concerns\ValidatesSankariDiscount;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSankariStoneSaleRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use ValidatesSankariDiscount;

    public function authorize(): bool
    {
        return $this->user()?->can('sankari.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('sale_date')) {
            $this->convertShamsiDates(['sale_date']);
        }
        $this->prepareSankariDiscount();
    }

    public function withValidator($validator): void
    {
        $this->validateSankariDiscount($validator);
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['sometimes', 'required', 'date'],
            'truck_count' => ['sometimes', 'required', 'integer', 'min:1'],
            'price_per_truck' => ['sometimes', 'required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\SankariStoneSale;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSankariStoneSaleRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('sankari.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['sale_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['sale_date']), [
            'truck_count' => ['required', 'integer', 'min:1'],
            'price_per_truck' => ['required', 'numeric', 'min:0'],
            'payment_type' => ['required', Rule::in(['cash', 'credit'])],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

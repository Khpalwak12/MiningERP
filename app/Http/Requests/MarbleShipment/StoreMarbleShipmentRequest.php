<?php

namespace App\Http\Requests\MarbleShipment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\NormalizesNullableShipmentAmounts;
use Illuminate\Foundation\Http\FormRequest;

class StoreMarbleShipmentRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use NormalizesNullableShipmentAmounts;

    public function authorize(): bool
    {
        return $this->user()?->can('shipments.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeNullableShipmentAmounts();
        $this->convertShamsiDates(['shipment_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['shipment_date']), [
            'customer_id' => ['required', 'exists:customers,id'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'quantity_ton' => ['nullable', 'numeric', 'min:0.001'],
            'price_per_ton' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

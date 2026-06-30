<?php

namespace App\Http\Requests\MarbleShipment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\NormalizesNullableShipmentAmounts;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarbleShipmentRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use NormalizesNullableShipmentAmounts;

    public function authorize(): bool
    {
        return $this->user()?->can('shipments.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeNullableShipmentAmounts();

        if ($this->filled('shipment_date')) {
            $this->convertShamsiDates(['shipment_date']);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
            'mine_type_id' => ['sometimes', 'required', 'exists:mine_types,id'],
            'shipment_date' => ['sometimes', 'required', 'date'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'quantity_ton' => ['nullable', 'numeric', 'min:0.001'],
            'price_per_ton' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

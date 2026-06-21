<?php

namespace App\Http\Requests\Concerns;

trait NormalizesNullableShipmentAmounts
{
    protected function normalizeNullableShipmentAmounts(): void
    {
        $this->merge([
            'quantity_ton' => $this->filled('quantity_ton') ? $this->input('quantity_ton') : null,
            'price_per_ton' => $this->filled('price_per_ton') ? $this->input('price_per_ton') : null,
        ]);
    }
}

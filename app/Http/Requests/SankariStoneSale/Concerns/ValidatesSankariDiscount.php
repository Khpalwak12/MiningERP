<?php

namespace App\Http\Requests\SankariStoneSale\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesSankariDiscount
{
    protected function prepareSankariDiscount(): void
    {
        if ($this->has('discount') && $this->input('discount') === '') {
            $this->merge(['discount' => 0]);
        }
    }

    protected function validateSankariDiscount(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('truck_count') || ! $this->filled('price_per_truck')) {
                return;
            }

            $subtotal = round((int) $this->input('truck_count') * (float) $this->input('price_per_truck'), 2);
            $discount = (float) ($this->input('discount') ?? 0);

            if ($discount > $subtotal) {
                $validator->errors()->add('discount', __('erp.sankari.discount_exceeds_subtotal'));
            }
        });
    }
}

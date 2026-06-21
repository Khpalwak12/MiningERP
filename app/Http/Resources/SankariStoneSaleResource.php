<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SankariStoneSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_date' => $this->sale_date?->format('Y-m-d'),
            'sale_date_shamsi' => JalaliDate::fromGregorian($this->sale_date),
            'truck_count' => (int) $this->truck_count,
            'price_per_truck' => (float) $this->price_per_truck,
            'total_amount' => (float) $this->total_amount,
            'payment_type' => $this->payment_type,
            'cash_received' => (float) $this->cash_received,
            'notes' => $this->notes,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarbleShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'shipment_date' => $this->shipment_date?->format('Y-m-d'),
            'shipment_date_shamsi' => JalaliDate::fromGregorian($this->shipment_date),
            'driver_name' => $this->driver_name,
            'quantity_ton' => $this->quantity_ton !== null ? (float) $this->quantity_ton : null,
            'price_per_ton' => $this->price_per_ton !== null ? (float) $this->price_per_ton : null,
            'total_amount' => $this->total_amount !== null ? (float) $this->total_amount : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

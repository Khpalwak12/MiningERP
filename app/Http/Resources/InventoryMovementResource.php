<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'movement_type' => $this->movement_type,
            'quantity' => (float) $this->quantity,
            'movement_date' => $this->movement_date?->format('Y-m-d'),
            'movement_date_shamsi' => JalaliDate::fromGregorian($this->movement_date),
            'reference' => $this->reference,
            'notes' => $this->notes,
            'inventory_item' => new InventoryItemResource($this->whenLoaded('inventoryItem')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

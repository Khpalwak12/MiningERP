<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'unit' => $this->unit,
            'category' => $this->category,
            'min_stock' => (float) $this->min_stock,
            'current_stock' => (float) $this->current_stock,
            'is_low_stock' => $this->current_stock <= $this->min_stock,
            'movements' => InventoryMovementResource::collection($this->whenLoaded('movements')),
            'created_at' => $this->created_at?->toISOString(),
            'created_at_shamsi' => JalaliDate::fromGregorian($this->created_at),
        ];
    }
}

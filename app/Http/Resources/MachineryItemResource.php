<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'purchase_date_shamsi' => JalaliDate::fromGregorian($this->purchase_date),
            'item_name' => $this->item_name,
            'bill_number' => $this->bill_number,
            'currency' => $this->currency,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'is_locked' => $this->isInClosedFinancialYear(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorProductionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'production_date' => $this->production_date?->format('Y-m-d'),
            'production_date_shamsi' => JalaliDate::fromGregorian($this->production_date),
            'truck_number' => $this->truck_number,
            'quantity_ton' => (float) $this->quantity_ton,
            'rate_per_ton' => (float) $this->rate_per_ton,
            'total_royalty' => (float) $this->total_royalty,
            'remarks' => $this->remarks,
            'is_locked' => $this->isInClosedFinancialYear(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

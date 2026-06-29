<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MineAssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'related_to' => $this->related_to,
            'quantity' => (float) $this->quantity,
            'unit' => $this->unit,
            'status' => $this->status,
            'registration_date' => $this->registration_date?->format('Y-m-d'),
            'registration_date_shamsi' => JalaliDate::fromGregorian($this->registration_date),
            'remarks' => $this->remarks,
            'is_locked' => $this->isInClosedFinancialYear(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

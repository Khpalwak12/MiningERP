<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entry_date' => $this->entry_date?->format('Y-m-d'),
            'entry_date_shamsi' => JalaliDate::fromGregorian($this->entry_date),
            'reference' => $this->reference,
            'description' => $this->description,
            'lines' => JournalEntryLineResource::collection($this->whenLoaded('lines')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

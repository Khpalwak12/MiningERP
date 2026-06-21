<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $description = $this->description;
        if (! is_string($description)) {
            $description = $description !== null ? json_encode($description) : '';
        }

        return [
            'id' => $this->id,
            'log_name' => (string) ($this->log_name ?? ''),
            'description' => $description,
            'causer_name' => $this->whenLoaded('causer', function () {
                if (! $this->causer) {
                    return '';
                }

                return (string) ($this->causer->name ?? $this->causer->email ?? '');
            }),
            'created_at' => $this->created_at?->toISOString(),
            'created_at_shamsi' => JalaliDate::fromGregorian($this->created_at) ?? '',
        ];
    }
}

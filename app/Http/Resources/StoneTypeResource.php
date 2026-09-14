<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoneTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'name_en' => $this->name_en,
            'name_ps' => $this->name_ps,
            'description' => $this->description,
            'slug' => $this->slug,
            'shipments_count' => $this->whenCounted('shipments'),
        ];
    }
}

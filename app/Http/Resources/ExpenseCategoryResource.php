<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'children' => ExpenseCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}

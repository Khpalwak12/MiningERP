<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'phone' => $this->phone,
            'position' => $this->position,
            'salary' => (float) $this->salary,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'joining_date_shamsi' => JalaliDate::fromGregorian($this->joining_date),
            'status' => $this->status,
            'total_paid' => (float) ($this->total_paid ?? 0),
            'remaining_salary' => (float) ($this->remaining_salary ?? 0),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

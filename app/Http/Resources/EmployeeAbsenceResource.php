<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAbsenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'absence_date' => $this->absence_date?->format('Y-m-d'),
            'absence_date_shamsi' => JalaliDate::fromGregorian($this->absence_date),
            'days' => (int) $this->days,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

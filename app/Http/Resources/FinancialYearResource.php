<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialYearResource extends JsonResource
{
    public static function optional(?\App\Models\FinancialYear $year): ?self
    {
        return $year ? self::make($year) : null;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'start_date_shamsi' => JalaliDate::fromGregorian($this->start_date),
            'end_date_shamsi' => JalaliDate::fromGregorian($this->end_date),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

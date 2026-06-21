<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'payment_date_shamsi' => JalaliDate::fromGregorian($this->payment_date),
            'amount' => (float) $this->amount,
            'payment_type' => $this->payment_type,
            'period_month' => $this->period_month,
            'notes' => $this->notes,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

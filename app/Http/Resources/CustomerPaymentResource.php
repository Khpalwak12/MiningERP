<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'payment_date_shamsi' => JalaliDate::fromGregorian($this->payment_date),
            'amount' => (float) $this->amount,
            'receipt_number' => $this->receipt_number,
            'received_by' => $this->received_by,
            'notes' => $this->notes,
            'is_locked' => $this->isInClosedFinancialYear(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

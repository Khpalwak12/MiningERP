<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractorPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'payment_date_shamsi' => JalaliDate::fromGregorian($this->payment_date),
            'amount' => (float) $this->amount,
            'receipt_number' => $this->receipt_number,
            'received_by' => $this->received_by,
            'remarks' => $this->remarks,
            'is_locked' => $this->isInClosedFinancialYear(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalLedgerTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'personal_contact_id' => $this->personal_contact_id,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'transaction_date_shamsi' => JalaliDate::fromGregorian($this->transaction_date),
            'transaction_type' => $this->transaction_type,
            'currency' => $this->currency,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'is_locked' => $this->isInClosedFinancialYear(),
            'contact' => new PersonalContactResource($this->whenLoaded('contact')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

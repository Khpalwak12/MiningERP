<?php

namespace App\Http\Resources;

use App\Models\PersonalLedgerTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $creditAfn = (float) ($this->credit_afn_sum ?? 0);
        $paymentAfn = (float) ($this->payment_afn_sum ?? 0);
        $creditUsd = (float) ($this->credit_usd_sum ?? 0);
        $paymentUsd = (float) ($this->payment_usd_sum ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'contact_type' => $this->contact_type,
            'notes' => $this->notes,
            'status' => $this->status,
            'balance_afn' => round($creditAfn - $paymentAfn, 2),
            'balance_usd' => round($creditUsd - $paymentUsd, 2),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

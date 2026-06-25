<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalSales = (float) ($this->total_sales ?? $this->total_sales_sum ?? $this->shipments_sum_total_amount ?? 0);
        $totalPayments = (float) ($this->total_payments ?? $this->total_payments_sum ?? $this->payments_sum_amount ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'status' => $this->status,
            'total_sales' => $totalSales,
            'total_payments' => $totalPayments,
            'outstanding_balance' => isset($this->outstanding_balance)
                ? (float) $this->outstanding_balance
                : $totalSales - $totalPayments,
            'shipments' => MarbleShipmentResource::collection($this->whenLoaded('shipments')),
            'payments' => CustomerPaymentResource::collection($this->whenLoaded('payments')),
            'ledger' => $this->when(isset($this->ledger), $this->ledger),
            'created_at' => $this->created_at?->toISOString(),
            'created_at_shamsi' => JalaliDate::fromGregorian($this->created_at),
        ];
    }
}

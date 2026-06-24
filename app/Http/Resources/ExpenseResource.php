<?php

namespace App\Http\Resources;

use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_category_id' => $this->expense_category_id,
            'subcategory' => $this->subcategory,
            'bill_number' => $this->bill_number,
            'expense_date' => $this->expense_date?->format('Y-m-d'),
            'expense_date_shamsi' => JalaliDate::fromGregorian($this->expense_date),
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'attachment' => $this->attachment,
            'is_locked' => $this->isInClosedFinancialYear(),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SankariStoneSale extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    protected $fillable = [
        'financial_year_id', 'sale_date', 'truck_count', 'price_per_truck', 'discount', 'total_amount',
        'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'price_per_truck' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SankariStoneSale $sale) {
            $subtotal = round((int) $sale->truck_count * (float) $sale->price_per_truck, 2);
            $discount = max(0, (float) ($sale->discount ?? 0));
            $sale->discount = $discount;
            $sale->total_amount = max(0, round($subtotal - $discount, 2));
        });
    }

    public function subtotal(): float
    {
        return round((int) $this->truck_count * (float) $this->price_per_truck, 2);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

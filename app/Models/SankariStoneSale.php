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
        'financial_year_id', 'sale_date', 'truck_count', 'price_per_truck', 'total_amount',
        'payment_type', 'cash_received', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'price_per_truck' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cash_received' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SankariStoneSale $sale) {
            $sale->total_amount = round($sale->truck_count * $sale->price_per_truck, 2);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

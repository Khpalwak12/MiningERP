<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorProduction extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    protected $fillable = [
        'financial_year_id', 'production_date', 'truck_number', 'quantity_ton',
        'rate_per_ton', 'total_royalty', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'quantity_ton' => 'decimal:3',
            'rate_per_ton' => 'decimal:2',
            'total_royalty' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function calculateTotalRoyalty(float $quantityTon, float $ratePerTon): float
    {
        return round($quantityTon * $ratePerTon, 2);
    }
}

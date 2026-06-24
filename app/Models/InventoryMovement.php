<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends AuditableModel
{
    use BelongsToFinancialYear;

    protected $fillable = [
        'financial_year_id', 'inventory_item_id', 'movement_type', 'quantity', 'movement_date', 'reference', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'quantity' => 'decimal:3',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (InventoryMovement $movement) {
            $item = $movement->inventoryItem;
            if ($movement->movement_type === 'in') {
                $item->increment('current_stock', $movement->quantity);
            } else {
                $item->decrement('current_stock', $movement->quantity);
            }
        });
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'unit', 'category', 'min_stock', 'current_stock',
    ];

    protected function casts(): array
    {
        return [
            'min_stock' => 'decimal:3',
            'current_stock' => 'decimal:3',
        ];
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}

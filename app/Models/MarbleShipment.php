<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarbleShipment extends AuditableModel
{
    use SoftDeletes;

    public const STATUS_PENDING_WEIGHT = 'pending_weight';

    public const STATUS_PENDING_PRICE = 'pending_price';

    public const STATUS_PENDING_BOTH = 'pending_both';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'customer_id', 'shipment_date',
        'driver_name', 'quantity_ton', 'price_per_ton', 'total_amount', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'shipment_date' => 'date',
            'quantity_ton' => 'decimal:3',
            'price_per_ton' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (MarbleShipment $shipment) {
            $shipment->syncTotalsAndStatus();
        });
    }

    public function syncTotalsAndStatus(): void
    {
        $hasWeight = $this->hasWeight();
        $hasPrice = $this->hasPrice();

        if ($hasWeight && $hasPrice) {
            $this->total_amount = round((float) $this->quantity_ton * (float) $this->price_per_ton, 2);
            $this->status = self::STATUS_COMPLETED;

            return;
        }

        $this->total_amount = null;

        if (! $hasWeight && $hasPrice) {
            $this->status = self::STATUS_PENDING_WEIGHT;
        } elseif ($hasWeight && ! $hasPrice) {
            $this->status = self::STATUS_PENDING_PRICE;
        } else {
            $this->status = self::STATUS_PENDING_BOTH;
        }
    }

    public function hasWeight(): bool
    {
        return $this->quantity_ton !== null && $this->quantity_ton !== '';
    }

    public function hasPrice(): bool
    {
        return $this->price_per_ton !== null && $this->price_per_ton !== '';
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends AuditableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'owner_name', 'phone', 'address', 'status',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(MarbleShipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function getTotalSalesAttribute(): float
    {
        return (float) $this->shipments()->completed()->sum('total_amount');
    }

    public function getTotalPaymentsAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return $this->total_sales - $this->total_payments;
    }
}

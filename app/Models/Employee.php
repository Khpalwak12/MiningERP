<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends AuditableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'father_name', 'phone', 'position', 'salary', 'joining_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'joining_date' => 'date',
        ];
    }

    public function payrollPayments(): HasMany
    {
        return $this->hasMany(PayrollPayment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        if (array_key_exists('total_paid_sum', $this->attributes)) {
            return (float) $this->attributes['total_paid_sum'];
        }

        if ($this->relationLoaded('payrollPayments')) {
            return (float) $this->payrollPayments->sum('amount');
        }

        return (float) $this->payrollPayments()->sum('amount');
    }

    public function scopeWithPayrollTotal($query)
    {
        return $query->withSum('payrollPayments as total_paid_sum', 'amount');
    }
}

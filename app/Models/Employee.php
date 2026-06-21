<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends AuditableModel
{
    use SoftDeletes;

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
        return (float) $this->payrollPayments()->sum('amount');
    }

    public function getRemainingSalaryAttribute(): float
    {
        return max(0, (float) $this->salary - $this->total_paid);
    }

    public function getOverpaidAmountAttribute(): float
    {
        return max(0, $this->total_paid - (float) $this->salary);
    }
}

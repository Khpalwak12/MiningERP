<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends AuditableModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'father_name', 'phone', 'position', 'salary', 'joining_date', 'end_date', 'status',
        'is_shared_with_contractor', 'contractor_salary_share_percent',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'joining_date' => 'date',
            'end_date' => 'date',
            'is_shared_with_contractor' => 'boolean',
            'contractor_salary_share_percent' => 'decimal:2',
        ];
    }

    public function contractorSalaryCharges(): HasMany
    {
        return $this->hasMany(ContractorSalaryCharge::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(EmployeeAbsence::class);
    }

    public function contractorSalaryShareForAmount(float $paymentAmount): float
    {
        if (! $this->is_shared_with_contractor) {
            return 0.0;
        }

        $percent = (float) ($this->contractor_salary_share_percent ?? 50);

        return round($paymentAmount * ($percent / 100), 2);
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

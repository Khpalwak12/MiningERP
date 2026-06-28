<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorSalaryCharge extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    protected $fillable = [
        'financial_year_id', 'employee_id', 'payroll_payment_id', 'charge_date',
        'period_month', 'amount', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'charge_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPayment(): BelongsTo
    {
        return $this->belongsTo(PayrollPayment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

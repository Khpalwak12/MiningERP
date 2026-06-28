<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorPayment extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    protected $fillable = [
        'financial_year_id', 'payment_date', 'amount', 'receipt_number',
        'received_by', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

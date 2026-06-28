<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    protected $fillable = [
        'financial_year_id', 'expense_category_id', 'subcategory', 'bill_number', 'expense_date',
        'amount', 'description', 'is_for_contractor', 'attachment', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
            'is_for_contractor' => 'boolean',
        ];
    }

    public function contractorExpenseCharge(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ContractorExpenseCharge::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForCompany($query)
    {
        return $query->where('is_for_contractor', false);
    }

    public function scopeForContractor($query)
    {
        return $query->where('is_for_contractor', true);
    }
}

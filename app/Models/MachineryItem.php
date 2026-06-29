<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineryItem extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    public const CURRENCY_AFN = 'AFN';

    public const CURRENCY_USD = 'USD';

    protected $fillable = [
        'financial_year_id', 'purchase_date', 'item_name', 'bill_number', 'currency', 'amount', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

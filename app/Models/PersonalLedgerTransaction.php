<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalLedgerTransaction extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    public const TYPE_CREDIT = 'credit';

    public const TYPE_PAYMENT = 'payment';

    public const CURRENCY_AFN = 'AFN';

    public const CURRENCY_USD = 'USD';

    protected $fillable = [
        'financial_year_id', 'personal_contact_id', 'transaction_date', 'transaction_type',
        'currency', 'amount', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(PersonalContact::class, 'personal_contact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCredit(): bool
    {
        return $this->transaction_type === self::TYPE_CREDIT;
    }
}

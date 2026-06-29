<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalContact extends AuditableModel
{
    use SoftDeletes;

    public const TYPE_PERSON = 'person';

    public const TYPE_SHOPKEEPER = 'shopkeeper';

    protected $fillable = [
        'name', 'phone', 'contact_type', 'notes', 'status',
    ];

    public function ledgerTransactions(): HasMany
    {
        return $this->hasMany(PersonalLedgerTransaction::class);
    }

    public function balanceForCurrency(string $currency): float
    {
        $credits = (float) $this->ledgerTransactions()
            ->where('currency', $currency)
            ->where('transaction_type', PersonalLedgerTransaction::TYPE_CREDIT)
            ->sum('amount');

        $payments = (float) $this->ledgerTransactions()
            ->where('currency', $currency)
            ->where('transaction_type', PersonalLedgerTransaction::TYPE_PAYMENT)
            ->sum('amount');

        return round($credits - $payments, 2);
    }
}

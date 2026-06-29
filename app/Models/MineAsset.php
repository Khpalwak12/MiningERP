<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MineAsset extends AuditableModel
{
    use BelongsToFinancialYear;
    use SoftDeletes;

    public const STATUS_USABLE = 'usable';

    public const STATUS_UNUSABLE = 'unusable';

    protected $fillable = [
        'financial_year_id', 'name', 'related_to', 'quantity', 'unit', 'status',
        'registration_date', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'registration_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isUsable(): bool
    {
        return $this->status === self::STATUS_USABLE;
    }
}

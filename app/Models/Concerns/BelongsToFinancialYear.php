<?php

namespace App\Models\Concerns;

use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToFinancialYear
{
    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function isInClosedFinancialYear(): bool
    {
        $this->loadMissing('financialYear');

        return $this->financialYear?->isClosed() ?? false;
    }
}

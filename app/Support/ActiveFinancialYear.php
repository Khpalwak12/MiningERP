<?php

namespace App\Support;

use App\Models\FinancialYear;

class ActiveFinancialYear
{
    public static function isAllYearsMode(): bool
    {
        return FinancialYear::query()
            ->active()
            ->where('is_all_years', true)
            ->exists();
    }

    public static function activeYearId(): ?int
    {
        if (self::isAllYearsMode()) {
            return null;
        }

        return FinancialYear::query()
            ->active()
            ->where('is_all_years', false)
            ->value('id');
    }

    public static function activeYear(): ?FinancialYear
    {
        if (self::isAllYearsMode()) {
            return null;
        }

        return FinancialYear::query()
            ->active()
            ->where('is_all_years', false)
            ->first();
    }

    public static function activeOption(): ?FinancialYear
    {
        return FinancialYear::query()->active()->first();
    }
}

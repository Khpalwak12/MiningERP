<?php

namespace App\Repositories\Concerns;

use App\Support\ActiveFinancialYear;
use Illuminate\Database\Eloquent\Builder;

trait AppliesFinancialYearFilter
{
    protected function applyFinancialYearFilter(Builder $query, array $filters, bool $defaultToActive = true): Builder
    {
        if (! empty($filters['financial_year_id'])) {
            return $query->where('financial_year_id', $filters['financial_year_id']);
        }

        if ($defaultToActive && ! ActiveFinancialYear::isAllYearsMode()) {
            $activeId = ActiveFinancialYear::activeYearId();

            if ($activeId) {
                $query->where('financial_year_id', $activeId);
            }
        }

        return $query;
    }
}

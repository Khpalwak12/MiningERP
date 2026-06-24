<?php

namespace App\Repositories\Concerns;

use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Builder;

trait AppliesFinancialYearFilter
{
    protected function applyFinancialYearFilter(Builder $query, array $filters, bool $defaultToActive = true): Builder
    {
        if (! empty($filters['financial_year_id'])) {
            return $query->where('financial_year_id', $filters['financial_year_id']);
        }

        if ($defaultToActive) {
            $activeId = FinancialYear::query()->active()->value('id');

            if ($activeId) {
                $query->where('financial_year_id', $activeId);
            }
        }

        return $query;
    }
}

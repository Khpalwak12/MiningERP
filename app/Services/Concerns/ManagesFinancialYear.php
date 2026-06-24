<?php

namespace App\Services\Concerns;

use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

trait ManagesFinancialYear
{
    protected function assignActiveFinancialYear(array $data): array
    {
        if (\App\Support\ActiveFinancialYear::isAllYearsMode()) {
            throw ValidationException::withMessages([
                'financial_year' => __('erp.financial_years.all_years_read_only'),
            ]);
        }

        $active = \App\Support\ActiveFinancialYear::activeYear();

        if (! $active) {
            throw ValidationException::withMessages([
                'financial_year' => __('erp.financial_years.no_active_year'),
            ]);
        }

        $data['financial_year_id'] = $active->id;

        return $data;
    }

    protected function ensureFinancialYearWritable(Model $model): void
    {
        if (\App\Support\ActiveFinancialYear::isAllYearsMode()) {
            throw ValidationException::withMessages([
                'financial_year' => __('erp.financial_years.all_years_read_only'),
            ]);
        }

        if (! method_exists($model, 'isInClosedFinancialYear')) {
            return;
        }

        if ($model->isInClosedFinancialYear()) {
            throw ValidationException::withMessages([
                'financial_year' => __('erp.financial_years.closed_year_locked'),
            ]);
        }
    }
}

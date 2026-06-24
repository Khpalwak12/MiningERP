<?php

namespace App\Http\Requests\Concerns;

use App\Support\ActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;

trait ValidatesActiveFinancialYear
{
    protected function ensureWritableFinancialYear(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (ActiveFinancialYear::isAllYearsMode()) {
                $validator->errors()->add('financial_year', __('erp.financial_years.all_years_read_only'));
            } elseif (! ActiveFinancialYear::activeYear()) {
                $validator->errors()->add('financial_year', __('erp.financial_years.no_active_year'));
            }
        });
    }
}

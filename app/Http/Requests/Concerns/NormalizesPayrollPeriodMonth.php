<?php

namespace App\Http\Requests\Concerns;

trait NormalizesPayrollPeriodMonth
{
    protected function normalizePayrollPeriodMonth(): void
    {
        if (! $this->filled('period_month')) {
            return;
        }

        $normalized = str_replace('-', '/', trim((string) $this->input('period_month')));

        if (preg_match('/^\d{4}\/\d{1,2}$/', $normalized)) {
            [$year, $month] = explode('/', $normalized);
            $normalized = sprintf('%s/%02d', $year, (int) $month);
        }

        $this->merge(['period_month' => $normalized]);
    }

    protected function payrollPeriodMonthRules(): array
    {
        return [
            'period_month' => ['nullable', 'string', 'regex:/^\d{4}\/\d{2}$/'],
        ];
    }
}

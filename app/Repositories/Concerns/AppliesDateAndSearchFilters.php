<?php

namespace App\Repositories\Concerns;

use App\Support\JalaliDate;
use Illuminate\Database\Eloquent\Builder;

trait AppliesDateAndSearchFilters
{
    protected function applyDateRange(Builder $query, array $filters, string $column): Builder
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate($column, '>=', JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate($column, '<=', JalaliDate::toGregorian($filters['date_to']));
        }

        return $query;
    }

    protected function applySearch(Builder $query, array $filters, array $columns): Builder
    {
        if (empty($filters['search'])) {
            return $query;
        }

        $search = $filters['search'];

        return $query->where(function (Builder $builder) use ($search, $columns) {
            foreach ($columns as $column) {
                $builder->orWhere($column, 'like', "%{$search}%");
            }
        });
    }

    protected function applyShamsiDateSearch(Builder $query, ?string $search, string $column): bool
    {
        if ($search === null || trim($search) === '') {
            return false;
        }

        $search = trim($search);

        if (preg_match('/^(\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2})\s*(?:-|–|—|→|to)\s*(\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2})$/iu', $search, $matches)) {
            $query->whereDate($column, '>=', JalaliDate::toGregorian($this->normalizeShamsiDate($matches[1])))
                ->whereDate($column, '<=', JalaliDate::toGregorian($this->normalizeShamsiDate($matches[2])));

            return true;
        }

        if (preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $search)) {
            $query->whereDate($column, JalaliDate::toGregorian($this->normalizeShamsiDate($search)));

            return true;
        }

        return false;
    }

    protected function normalizeShamsiDate(string $date): string
    {
        $parts = array_map('intval', explode('/', str_replace('-', '/', $date)));

        return sprintf('%04d/%02d/%02d', $parts[0], $parts[1], $parts[2]);
    }
}

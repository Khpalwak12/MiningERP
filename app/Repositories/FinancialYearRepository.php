<?php

namespace App\Repositories;

use App\Contracts\Repositories\FinancialYearRepositoryInterface;
use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Builder;

class FinancialYearRepository extends BaseRepository implements FinancialYearRepositoryInterface
{
    public function __construct(FinancialYear $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery(), $filters)
            ->orderBy('is_all_years')
            ->orderByDesc('start_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}

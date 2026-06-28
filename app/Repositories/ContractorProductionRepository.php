<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContractorProductionRepositoryInterface;
use App\Models\ContractorProduction;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class ContractorProductionRepository extends BaseRepository implements ContractorProductionRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(ContractorProduction $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['creator', 'financialYear']), $filters)
            ->latest('production_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                foreach (['remarks'] as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $this->applyDateRange($query, $filters, 'production_date');
    }
}

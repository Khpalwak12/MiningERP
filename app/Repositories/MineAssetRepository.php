<?php

namespace App\Repositories;

use App\Contracts\Repositories\MineAssetRepositoryInterface;
use App\Models\MineAsset;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class MineAssetRepository extends BaseRepository implements MineAssetRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(MineAsset $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['creator', 'financialYear']), $filters)
            ->latest('registration_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);
        $query = $this->applySearch($query, $filters, ['name', 'related_to', 'unit', 'remarks']);

        if (! empty($filters['status']) && in_array($filters['status'], [MineAsset::STATUS_USABLE, MineAsset::STATUS_UNUSABLE], true)) {
            $query->where('status', $filters['status']);
        }

        return $this->applyDateRange($query, $filters, 'registration_date');
    }
}

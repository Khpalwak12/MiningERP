<?php

namespace App\Repositories;

use App\Contracts\Repositories\MachineryItemRepositoryInterface;
use App\Models\MachineryItem;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class MachineryItemRepository extends BaseRepository implements MachineryItemRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(MachineryItem $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with('creator'), $filters)
            ->latest('purchase_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);
        $query = $this->applySearch($query, $filters, ['item_name', 'bill_number', 'description']);

        if (! empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        return $this->applyDateRange($query, $filters, 'purchase_date');
    }
}

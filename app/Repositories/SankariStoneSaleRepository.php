<?php

namespace App\Repositories;

use App\Contracts\Repositories\SankariStoneSaleRepositoryInterface;
use App\Models\SankariStoneSale;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use Illuminate\Database\Eloquent\Builder;

class SankariStoneSaleRepository extends BaseRepository implements SankariStoneSaleRepositoryInterface
{
    use AppliesDateAndSearchFilters;

    public function __construct(SankariStoneSale $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with('creator'), $filters)
            ->latest('sale_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $query = $this->applySearch($query, $filters, ['notes']);

        if (! empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        return $this->applyDateRange($query, $filters, 'sale_date');
    }
}

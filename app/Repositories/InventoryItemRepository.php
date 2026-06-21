<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryItemRepositoryInterface;
use App\Models\InventoryItem;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use Illuminate\Database\Eloquent\Builder;

class InventoryItemRepository extends BaseRepository implements InventoryItemRepositoryInterface
{
    use AppliesDateAndSearchFilters;

    public function __construct(InventoryItem $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $query = $this->applySearch($query, $filters, ['name', 'sku', 'category', 'unit']);

        if (! empty($filters['low_stock'])) {
            $query->whereColumn('current_stock', '<=', 'min_stock');
        }

        return $this->applyDateRange($query, $filters, 'created_at');
    }
}

<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryItemRepositoryInterface;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    use AppliesFinancialYearFilter;
    use ManagesFinancialYear;
    public function __construct(private InventoryItemRepositoryInterface $repository) {}

    public function paginateItems(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findItem(int $id): ?InventoryItem
    {
        return $this->repository->find($id);
    }

    public function createItem(array $data): InventoryItem
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function updateItem(InventoryItem $item, array $data): InventoryItem
    {
        return DB::transaction(fn () => $this->repository->update($item, $data));
    }

    public function deleteItem(InventoryItem $item): bool
    {
        return DB::transaction(fn () => $this->repository->delete($item));
    }

    public function recordMovement(array $data): InventoryMovement
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return InventoryMovement::query()->create($data);
        });
    }

    public function paginateMovements(int $itemId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryMovement::query()
            ->with(['inventoryItem', 'creator', 'financialYear'])
            ->where('inventory_item_id', $itemId);

        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('movement_date', '>=', \App\Support\JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('movement_date', '<=', \App\Support\JalaliDate::toGregorian($filters['date_to']));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        return $query->latest('movement_date')->paginate($perPage)->withQueryString();
    }
}

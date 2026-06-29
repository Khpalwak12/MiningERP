<?php

namespace App\Services;

use App\Contracts\Repositories\MachineryItemRepositoryInterface;
use App\Models\MachineryItem;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MachineryItemService
{
    use ManagesFinancialYear;

    public function __construct(private MachineryItemRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): MachineryItem
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(MachineryItem $item, array $data): MachineryItem
    {
        $this->ensureFinancialYearWritable($item);

        return DB::transaction(fn () => $this->repository->update($item, $data));
    }

    public function delete(MachineryItem $item): bool
    {
        $this->ensureFinancialYearWritable($item);

        return DB::transaction(fn () => $this->repository->delete($item));
    }

    public function totalsByCurrency(?int $financialYearId = null): array
    {
        $query = MachineryItem::query();

        if ($financialYearId) {
            $query->where('financial_year_id', $financialYearId);
        }

        return [
            'AFN' => (float) (clone $query)->where('currency', MachineryItem::CURRENCY_AFN)->sum('amount'),
            'USD' => (float) (clone $query)->where('currency', MachineryItem::CURRENCY_USD)->sum('amount'),
        ];
    }
}

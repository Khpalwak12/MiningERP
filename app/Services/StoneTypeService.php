<?php

namespace App\Services;

use App\Contracts\Repositories\StoneTypeRepositoryInterface;
use App\Models\StoneType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoneTypeService
{
    public function __construct(private StoneTypeRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): StoneType
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(StoneType $stoneType, array $data): StoneType
    {
        return DB::transaction(fn () => $this->repository->update($stoneType, $data));
    }

    public function delete(StoneType $stoneType): void
    {
        if ($stoneType->shipments()->exists()) {
            throw new \InvalidArgumentException(__('erp.stone_types.in_use'));
        }

        DB::transaction(fn () => $this->repository->delete($stoneType));
    }
}

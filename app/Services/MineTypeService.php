<?php

namespace App\Services;

use App\Contracts\Repositories\MineTypeRepositoryInterface;
use App\Models\MineType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MineTypeService
{
    public function __construct(private MineTypeRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): MineType
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(MineType $mineType, array $data): MineType
    {
        return DB::transaction(fn () => $this->repository->update($mineType, $data));
    }

    public function delete(MineType $mineType): void
    {
        if ($mineType->shipments()->exists()) {
            throw new \InvalidArgumentException(__('erp.mine_types.in_use'));
        }

        DB::transaction(fn () => $this->repository->delete($mineType));
    }
}

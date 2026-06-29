<?php

namespace App\Services;

use App\Contracts\Repositories\MineAssetRepositoryInterface;
use App\Models\MineAsset;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MineAssetService
{
    use ManagesFinancialYear;

    public function __construct(private MineAssetRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?MineAsset
    {
        return $this->repository->find($id);
    }

    public function create(array $data): MineAsset
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(MineAsset $asset, array $data): MineAsset
    {
        $this->ensureFinancialYearWritable($asset);

        return DB::transaction(fn () => $this->repository->update($asset, $data));
    }

    public function delete(MineAsset $asset): bool
    {
        $this->ensureFinancialYearWritable($asset);

        return DB::transaction(fn () => $this->repository->delete($asset));
    }
}

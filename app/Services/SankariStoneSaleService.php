<?php

namespace App\Services;

use App\Contracts\Repositories\SankariStoneSaleRepositoryInterface;
use App\Models\SankariStoneSale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SankariStoneSaleService
{
    public function __construct(private SankariStoneSaleRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?SankariStoneSale
    {
        return $this->repository->find($id);
    }

    public function create(array $data): SankariStoneSale
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();

            return $this->repository->create($data);
        });
    }

    public function update(SankariStoneSale $sale, array $data): SankariStoneSale
    {
        return DB::transaction(fn () => $this->repository->update($sale, $data));
    }

    public function delete(SankariStoneSale $sale): bool
    {
        return DB::transaction(fn () => $this->repository->delete($sale));
    }
}

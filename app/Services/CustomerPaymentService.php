<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerPaymentRepositoryInterface;
use App\Models\CustomerPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerPaymentService
{
    public function __construct(private CustomerPaymentRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?CustomerPayment
    {
        return $this->repository->find($id);
    }

    public function create(array $data): CustomerPayment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();

            return $this->repository->create($data);
        });
    }

    public function update(CustomerPayment $payment, array $data): CustomerPayment
    {
        return DB::transaction(fn () => $this->repository->update($payment, $data));
    }

    public function delete(CustomerPayment $payment): bool
    {
        return DB::transaction(fn () => $this->repository->delete($payment));
    }
}

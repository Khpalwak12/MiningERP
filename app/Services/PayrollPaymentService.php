<?php

namespace App\Services;

use App\Contracts\Repositories\PayrollPaymentRepositoryInterface;
use App\Models\PayrollPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PayrollPaymentService
{
    public function __construct(private PayrollPaymentRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?PayrollPayment
    {
        return $this->repository->find($id);
    }

    public function create(array $data): PayrollPayment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();

            return $this->repository->create($data);
        });
    }

    public function update(PayrollPayment $payment, array $data): PayrollPayment
    {
        return DB::transaction(fn () => $this->repository->update($payment, $data));
    }

    public function delete(PayrollPayment $payment): bool
    {
        return DB::transaction(fn () => $this->repository->delete($payment));
    }
}

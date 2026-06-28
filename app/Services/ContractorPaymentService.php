<?php

namespace App\Services;

use App\Contracts\Repositories\ContractorPaymentRepositoryInterface;
use App\Models\ContractorPayment;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ContractorPaymentService
{
    use ManagesFinancialYear;

    public function __construct(private ContractorPaymentRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): ContractorPayment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(ContractorPayment $payment, array $data): ContractorPayment
    {
        $this->ensureFinancialYearWritable($payment);

        return DB::transaction(function () use ($payment, $data) {
            return $this->repository->update($payment, $data);
        });
    }

    public function delete(ContractorPayment $payment): bool
    {
        $this->ensureFinancialYearWritable($payment);

        return $this->repository->delete($payment);
    }
}

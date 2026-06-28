<?php

namespace App\Services;

use App\Contracts\Repositories\PayrollPaymentRepositoryInterface;
use App\Models\PayrollPayment;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PayrollPaymentService
{
    use ManagesFinancialYear;

    public function __construct(
        private PayrollPaymentRepositoryInterface $repository,
        private ContractorSalaryChargeService $contractorSalaryChargeService,
    ) {}

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
            $data = $this->assignActiveFinancialYear($data);

            $payment = $this->repository->create($data);
            $this->contractorSalaryChargeService->syncFromPayrollPayment($payment);

            return $payment;
        });
    }

    public function update(PayrollPayment $payment, array $data): PayrollPayment
    {
        $this->ensureFinancialYearWritable($payment);

        return DB::transaction(function () use ($payment, $data) {
            $payment = $this->repository->update($payment, $data);
            $this->contractorSalaryChargeService->syncFromPayrollPayment($payment->fresh(['employee']));

            return $payment;
        });
    }

    public function delete(PayrollPayment $payment): bool
    {
        $this->ensureFinancialYearWritable($payment);

        return DB::transaction(function () use ($payment) {
            $this->contractorSalaryChargeService->removeForPayrollPayment($payment);

            return $this->repository->delete($payment);
        });
    }
}

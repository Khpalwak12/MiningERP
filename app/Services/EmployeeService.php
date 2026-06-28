<?php

namespace App\Services;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $repository,
        private ContractorSalaryChargeService $contractorSalaryChargeService,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?Employee
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Employee
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee = $this->repository->update($employee, $data);

            if (array_key_exists('is_shared_with_contractor', $data) || array_key_exists('contractor_salary_share_percent', $data)) {
                $employee->payrollPayments()->each(
                    fn ($payment) => $this->contractorSalaryChargeService->syncFromPayrollPayment($payment)
                );
            }

            return $employee;
        });
    }

    public function delete(Employee $employee): bool
    {
        return DB::transaction(fn () => $this->repository->delete($employee));
    }
}

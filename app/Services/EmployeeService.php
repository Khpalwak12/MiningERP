<?php

namespace App\Services;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function __construct(private EmployeeRepositoryInterface $repository) {}

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
        return DB::transaction(fn () => $this->repository->update($employee, $data));
    }

    public function delete(Employee $employee): bool
    {
        return DB::transaction(fn () => $this->repository->delete($employee));
    }
}

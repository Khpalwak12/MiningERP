<?php

namespace App\Services;

use App\Contracts\Repositories\PersonalHomeExpenseRepositoryInterface;
use App\Models\PersonalHomeExpense;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PersonalHomeExpenseService
{
    use ManagesFinancialYear;

    public function __construct(private PersonalHomeExpenseRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): PersonalHomeExpense
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data['currency'] = 'AFN';
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(PersonalHomeExpense $expense, array $data): PersonalHomeExpense
    {
        $this->ensureFinancialYearWritable($expense);

        return DB::transaction(function () use ($expense, $data) {
            $data['currency'] = 'AFN';

            return $this->repository->update($expense, $data);
        });
    }

    public function delete(PersonalHomeExpense $expense): bool
    {
        $this->ensureFinancialYearWritable($expense);

        return DB::transaction(fn () => $this->repository->delete($expense));
    }
}

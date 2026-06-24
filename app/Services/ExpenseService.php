<?php

namespace App\Services;

use App\Contracts\Repositories\ExpenseRepositoryInterface;
use App\Models\Expense;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    use ManagesFinancialYear;
    public function __construct(private ExpenseRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function find(int $id): ?Expense
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);
            $data = $this->handleAttachment($data);

            return $this->repository->create($data);
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        $this->ensureFinancialYearWritable($expense);

        return DB::transaction(function () use ($expense, $data) {
            if (isset($data['attachment']) && $expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }

            $data = $this->handleAttachment($data);

            return $this->repository->update($expense, $data);
        });
    }

    public function delete(Expense $expense): bool
    {
        $this->ensureFinancialYearWritable($expense);

        return DB::transaction(function () use ($expense) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }

            return $this->repository->delete($expense);
        });
    }

    private function handleAttachment(array $data): array
    {
        if (isset($data['attachment']) && is_object($data['attachment'])) {
            $data['attachment'] = $data['attachment']->store('expenses', 'public');
        }

        return $data;
    }
}

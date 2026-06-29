<?php

namespace App\Services;

use App\Contracts\Repositories\ExpenseCategoryRepositoryInterface;
use App\Models\ExpenseCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryService
{
    public function __construct(private ExpenseCategoryRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): ExpenseCategory
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(ExpenseCategory $category, array $data): ExpenseCategory
    {
        return DB::transaction(fn () => $this->repository->update($category, $data));
    }

    public function delete(ExpenseCategory $category): void
    {
        if ($category->expenses()->exists()) {
            throw new \InvalidArgumentException(__('erp.expense_categories.in_use'));
        }

        DB::transaction(fn () => $this->repository->delete($category));
    }
}

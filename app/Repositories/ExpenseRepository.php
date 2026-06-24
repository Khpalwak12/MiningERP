<?php

namespace App\Repositories;

use App\Contracts\Repositories\ExpenseRepositoryInterface;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['category', 'creator', 'financialYear']), $filters)
            ->latest('expense_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            if (! $this->applyShamsiDateSearch($query, $search, 'expense_date')) {
                $query->where(function (Builder $builder) use ($search) {
                    foreach (['description', 'subcategory', 'bill_number'] as $column) {
                        $builder->orWhere($column, 'like', "%{$search}%");
                    }

                    if (is_numeric($search)) {
                        $builder->orWhere('amount', $search);
                    }

                    $categoryIds = ExpenseCategory::parentIdsMatchingSearch($search);

                    if ($categoryIds !== []) {
                        $builder->orWhereIn('expense_category_id', $categoryIds);
                    } else {
                        $builder->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', "%{$search}%");
                        });
                    }
                });
            }
        }

        if (! empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        if (! empty($filters['subcategory'])) {
            $query->where('subcategory', 'like', '%'.$filters['subcategory'].'%');
        }

        return $this->applyDateRange($query, $filters, 'expense_date');
    }
}

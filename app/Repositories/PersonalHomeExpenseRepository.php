<?php

namespace App\Repositories;

use App\Contracts\Repositories\PersonalHomeExpenseRepositoryInterface;
use App\Models\PersonalHomeExpense;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class PersonalHomeExpenseRepository extends BaseRepository implements PersonalHomeExpenseRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(PersonalHomeExpense $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with('creator'), $filters)
            ->latest('expense_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);
        $query = $this->applySearch($query, $filters, ['item_name', 'description']);

        return $this->applyDateRange($query, $filters, 'expense_date');
    }
}

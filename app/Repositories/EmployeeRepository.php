<?php

namespace App\Repositories;

use App\Contracts\Repositories\EmployeeRepositoryInterface;
use App\Models\Employee;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use Illuminate\Database\Eloquent\Builder;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    use AppliesDateAndSearchFilters;

    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $query = $this->applySearch($query, $filters, ['name', 'father_name', 'phone', 'position']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $this->applyDateRange($query, $filters, 'joining_date');
    }
}

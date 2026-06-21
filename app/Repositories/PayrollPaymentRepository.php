<?php

namespace App\Repositories;

use App\Contracts\Repositories\PayrollPaymentRepositoryInterface;
use App\Models\PayrollPayment;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use Illuminate\Database\Eloquent\Builder;

class PayrollPaymentRepository extends BaseRepository implements PayrollPaymentRepositoryInterface
{
    use AppliesDateAndSearchFilters;

    public function __construct(PayrollPayment $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['employee', 'creator']), $filters)
            ->latest('payment_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $query = $this->applySearch($query, $filters, ['notes', 'period_month']);

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (! empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        return $this->applyDateRange($query, $filters, 'payment_date');
    }
}

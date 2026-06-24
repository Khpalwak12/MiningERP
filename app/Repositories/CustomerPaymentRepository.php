<?php

namespace App\Repositories;

use App\Contracts\Repositories\CustomerPaymentRepositoryInterface;
use App\Models\CustomerPayment;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class CustomerPaymentRepository extends BaseRepository implements CustomerPaymentRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(CustomerPayment $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['customer', 'creator', 'financialYear']), $filters)
            ->latest('payment_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search) {
                foreach (['notes', 'receipt_number', 'received_by'] as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }

                $builder->orWhereHas('customer', function (Builder $customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $this->applyDateRange($query, $filters, 'payment_date');
    }
}

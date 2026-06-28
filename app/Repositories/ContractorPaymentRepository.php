<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContractorPaymentRepositoryInterface;
use App\Models\ContractorPayment;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class ContractorPaymentRepository extends BaseRepository implements ContractorPaymentRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(ContractorPayment $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['creator', 'financialYear']), $filters)
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
                foreach (['receipt_number', 'received_by', 'remarks'] as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $this->applyDateRange($query, $filters, 'payment_date');
    }
}

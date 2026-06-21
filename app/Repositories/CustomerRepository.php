<?php

namespace App\Repositories;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters)
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function withBalances(array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(
            $this->model->newQuery()
                ->withSum(['shipments as total_sales_sum' => fn ($q) => $q->completed()], 'total_amount')
                ->withSum('payments as total_payments_sum', 'amount'),
            $filters
        )->latest('id')->paginate($filters['per_page'] ?? 15)->withQueryString();
    }
}

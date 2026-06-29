<?php

namespace App\Repositories;

use App\Contracts\Repositories\PersonalLedgerTransactionRepositoryInterface;
use App\Models\PersonalLedgerTransaction;
use App\Repositories\Concerns\AppliesDateAndSearchFilters;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Illuminate\Database\Eloquent\Builder;

class PersonalLedgerTransactionRepository extends BaseRepository implements PersonalLedgerTransactionRepositoryInterface
{
    use AppliesDateAndSearchFilters;
    use AppliesFinancialYearFilter;

    public function __construct(PersonalLedgerTransaction $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery()->with(['contact', 'creator']), $filters)
            ->latest('transaction_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['personal_contact_id'])) {
            $query->where('personal_contact_id', $filters['personal_contact_id']);
        }

        if (! empty($filters['currency'])) {
            $query->where('currency', $filters['currency']);
        }

        if (! empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        $query = $this->applySearch($query, $filters, ['description']);

        return $this->applyDateRange($query, $filters, 'transaction_date');
    }
}

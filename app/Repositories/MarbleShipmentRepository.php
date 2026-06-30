<?php

namespace App\Repositories;

use App\Contracts\Repositories\MarbleShipmentRepositoryInterface;
use App\Models\MarbleShipment;
use App\Support\JalaliDate;
use App\Repositories\Concerns\AppliesFinancialYearFilter;
use Carbon\Carbon;

class MarbleShipmentRepository extends BaseRepository implements MarbleShipmentRepositoryInterface
{
    use AppliesFinancialYearFilter;
    public function __construct(MarbleShipment $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters($query, array $filters)
    {
        $this->applyFinancialYearFilter($query, $filters);

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('shipment_date', '>=', JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->where('shipment_date', '<=', JalaliDate::toGregorian($filters['date_to']));
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'pending') {
                $query->pending();
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('driver_name', 'like', '%'.$filters['search'].'%');
            });
        }

        return $query->with(['customer', 'mineType', 'financialYear']);
    }

    public function todayStats(?int $financialYearId = null): array
    {
        $todayShamsi = JalaliDate::fromGregorian(Carbon::today());
        $todayGregorian = JalaliDate::toGregorian($todayShamsi);

        $query = $this->model->newQuery()
            ->whereDate('shipment_date', $todayGregorian)
            ->when($financialYearId, fn ($q) => $q->where('financial_year_id', $financialYearId));

        return [
            'trucks' => (clone $query)->count(),
            'tons' => (float) (clone $query)->whereNotNull('quantity_ton')->sum('quantity_ton'),
            'sales' => (float) (clone $query)->completed()->sum('total_amount'),
        ];
    }

    public function monthlyStats(string $shamsiMonth, ?int $financialYearId = null): array
    {
        [$start, $end] = JalaliDate::monthRange($shamsiMonth);

        $query = $this->model->newQuery()
            ->whereBetween('shipment_date', [$start, $end])
            ->when($financialYearId, fn ($q) => $q->where('financial_year_id', $financialYearId));

        return [
            'trucks' => (clone $query)->count(),
            'tons' => (float) (clone $query)->whereNotNull('quantity_ton')->sum('quantity_ton'),
            'sales' => (float) (clone $query)->completed()->sum('total_amount'),
        ];
    }
}

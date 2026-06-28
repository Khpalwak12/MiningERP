<?php

namespace App\Services;

use App\Contracts\Repositories\ContractorPaymentRepositoryInterface;
use App\Contracts\Repositories\ContractorProductionRepositoryInterface;
use App\Models\ContractorPayment;
use App\Models\ContractorProduction;
use App\Services\Concerns\ManagesFinancialYear;
use App\Support\ActiveFinancialYear;
use App\Support\JalaliDate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContractorProductionService
{
    use ManagesFinancialYear;

    public function __construct(private ContractorProductionRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): ContractorProduction
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);
            $data = $this->applyRoyaltyCalculation($data);

            return $this->repository->create($data);
        });
    }

    public function update(ContractorProduction $production, array $data): ContractorProduction
    {
        $this->ensureFinancialYearWritable($production);

        return DB::transaction(function () use ($production, $data) {
            $data = $this->applyRoyaltyCalculation(array_merge([
                'quantity_ton' => $production->quantity_ton,
                'rate_per_ton' => $production->rate_per_ton,
            ], $data));

            return $this->repository->update($production, $data);
        });
    }

    public function delete(ContractorProduction $production): bool
    {
        $this->ensureFinancialYearWritable($production);

        return $this->repository->delete($production);
    }

    public function defaultRatePerTon(): float
    {
        return (float) config('erp.contractor_royalty.default_rate_per_ton', 150);
    }

    private function applyRoyaltyCalculation(array $data): array
    {
        $quantity = (float) ($data['quantity_ton'] ?? 0);
        $rate = (float) ($data['rate_per_ton'] ?? $this->defaultRatePerTon());
        $data['rate_per_ton'] = $rate;
        $data['total_royalty'] = ContractorProduction::calculateTotalRoyalty($quantity, $rate);

        return $data;
    }
}

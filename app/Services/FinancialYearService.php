<?php

namespace App\Services;

use App\Contracts\Repositories\FinancialYearRepositoryInterface;
use App\Models\FinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinancialYearService
{
    public function __construct(private FinancialYearRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function all(): \Illuminate\Support\Collection
    {
        return $this->repository->all()->sortByDesc('start_date')->values();
    }

    public function find(int $id): ?FinancialYear
    {
        return $this->repository->find($id);
    }

    public function active(): ?FinancialYear
    {
        return FinancialYear::query()->active()->first();
    }

    public function create(array $data): FinancialYear
    {
        return DB::transaction(function () use ($data) {
            if (($data['status'] ?? FinancialYear::STATUS_ACTIVE) === FinancialYear::STATUS_ACTIVE) {
                $this->deactivateOtherYears();
            }

            return $this->repository->create($data);
        });
    }

    public function update(FinancialYear $financialYear, array $data): FinancialYear
    {
        if ($financialYear->isClosed()) {
            throw ValidationException::withMessages([
                'status' => __('erp.financial_years.closed_year_locked'),
            ]);
        }

        return DB::transaction(function () use ($financialYear, $data) {
            if (($data['status'] ?? $financialYear->status) === FinancialYear::STATUS_ACTIVE) {
                $this->deactivateOtherYears($financialYear->id);
            }

            return $this->repository->update($financialYear, $data);
        });
    }

    public function close(FinancialYear $financialYear): FinancialYear
    {
        if ($financialYear->isClosed()) {
            throw ValidationException::withMessages([
                'status' => __('erp.financial_years.already_closed'),
            ]);
        }

        return DB::transaction(function () use ($financialYear) {
            return $this->repository->update($financialYear, [
                'status' => FinancialYear::STATUS_CLOSED,
            ]);
        });
    }

    public function activate(FinancialYear $financialYear): FinancialYear
    {
        if ($financialYear->isActive()) {
            return $financialYear;
        }

        return DB::transaction(function () use ($financialYear) {
            $this->deactivateOtherYears($financialYear->id);

            return $this->repository->update($financialYear, [
                'status' => FinancialYear::STATUS_ACTIVE,
            ]);
        });
    }

    private function deactivateOtherYears(?int $exceptId = null): void
    {
        $query = FinancialYear::query()->where('status', FinancialYear::STATUS_ACTIVE);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        $query->update(['status' => FinancialYear::STATUS_CLOSED]);
    }
}

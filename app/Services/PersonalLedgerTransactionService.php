<?php

namespace App\Services;

use App\Contracts\Repositories\PersonalLedgerTransactionRepositoryInterface;
use App\Models\PersonalLedgerTransaction;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PersonalLedgerTransactionService
{
    use ManagesFinancialYear;

    public function __construct(private PersonalLedgerTransactionRepositoryInterface $repository) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(array $data): PersonalLedgerTransaction
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(PersonalLedgerTransaction $transaction, array $data): PersonalLedgerTransaction
    {
        $this->ensureFinancialYearWritable($transaction);

        return DB::transaction(fn () => $this->repository->update($transaction, $data));
    }

    public function delete(PersonalLedgerTransaction $transaction): bool
    {
        $this->ensureFinancialYearWritable($transaction);

        return DB::transaction(fn () => $this->repository->delete($transaction));
    }
}

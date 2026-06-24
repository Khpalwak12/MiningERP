<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    use ManagesFinancialYear;
    public function paginateEntries(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = JournalEntry::query()->with(['lines.account', 'creator', 'financialYear']);

        if (! empty($filters['financial_year_id'])) {
            $query->where('financial_year_id', $filters['financial_year_id']);
        } elseif ($activeId = \App\Models\FinancialYear::query()->active()->value('id')) {
            $query->where('financial_year_id', $activeId);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('entry_date', '>=', \App\Support\JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('entry_date', '<=', \App\Support\JalaliDate::toGregorian($filters['date_to']));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest('entry_date')->paginate($perPage)->withQueryString();
    }

    public function findEntry(int $id): ?JournalEntry
    {
        return JournalEntry::query()->with(['lines.account', 'creator'])->find($id);
    }

    public function getAccounts(): Collection
    {
        return Account::query()->where('is_active', true)->orderBy('code')->get();
    }

    public function createEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $lines = $data['lines'] ?? [];
            unset($data['lines']);

            $data['created_by'] = $data['created_by'] ?? auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            $this->validateBalancedEntry($lines);

            $entry = JournalEntry::query()->create($data);

            foreach ($lines as $line) {
                $entry->lines()->create($line);
            }

            return $entry->load(['lines.account', 'creator']);
        });
    }

    public function updateEntry(JournalEntry $entry, array $data): JournalEntry
    {
        $this->ensureFinancialYearWritable($entry);

        return DB::transaction(function () use ($entry, $data) {
            $lines = $data['lines'] ?? null;
            unset($data['lines']);

            $entry->update($data);

            if ($lines !== null) {
                $this->validateBalancedEntry($lines);
                $entry->lines()->delete();

                foreach ($lines as $line) {
                    $entry->lines()->create($line);
                }
            }

            return $entry->fresh(['lines.account', 'creator']);
        });
    }

    public function deleteEntry(JournalEntry $entry): bool
    {
        $this->ensureFinancialYearWritable($entry);

        return DB::transaction(function () use ($entry) {
            $entry->lines()->delete();

            return (bool) $entry->delete();
        });
    }

    public function trialBalance(array $filters = []): Collection
    {
        $query = JournalEntryLine::query()
            ->selectRaw('account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('account_id')
            ->with('account');

        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $query->whereHas('journalEntry', function ($q) use ($filters) {
                if (! empty($filters['date_from'])) {
                    $q->whereDate('entry_date', '>=', \App\Support\JalaliDate::toGregorian($filters['date_from']));
                }
                if (! empty($filters['date_to'])) {
                    $q->whereDate('entry_date', '<=', \App\Support\JalaliDate::toGregorian($filters['date_to']));
                }
            });
        }

        return $query->get();
    }

    private function validateBalancedEntry(array $lines): void
    {
        $totalDebit = collect($lines)->sum('debit');
        $totalCredit = collect($lines)->sum('credit');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \InvalidArgumentException(__('erp.accounting.unbalanced_entry'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Accounting\StoreJournalEntryRequest;
use App\Http\Requests\Accounting\UpdateJournalEntryRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountingController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private AccountingService $service)
    {
        $this->registerModulePermissions('accounting');
        $this->middleware('permission:accounting.view')->only('trialBalance');
    }

    public function index(Request $request): Response
    {
        $entries = $this->service->paginateEntries($request->only(['search', 'date_from', 'date_to']));

        return Inertia::render('Accounting/Index', [
            'entries' => JournalEntryResource::collection($entries),
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Accounting/Create', [
            'accounts' => AccountResource::collection($this->service->getAccounts()),
        ]);
    }

    public function store(StoreJournalEntryRequest $request): RedirectResponse
    {
        try {
            $this->service->createEntry($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['lines' => $e->getMessage()]);
        }

        return redirect()->route('journal-entries.index')->with('success', __('erp.accounting.created'));
    }

    public function show(JournalEntry $journalEntry): Response
    {
        $entry = $this->service->findEntry($journalEntry->id);

        return Inertia::render('Accounting/Show', [
            'entry' => new JournalEntryResource($entry),
        ]);
    }

    public function edit(JournalEntry $journalEntry): Response
    {
        $entry = $this->service->findEntry($journalEntry->id);

        return Inertia::render('Accounting/Edit', [
            'entry' => new JournalEntryResource($entry),
            'accounts' => AccountResource::collection($this->service->getAccounts()),
        ]);
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {
        try {
            $this->service->updateEntry($journalEntry, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['lines' => $e->getMessage()]);
        }

        return redirect()->route('journal-entries.index')->with('success', __('erp.accounting.updated'));
    }

    public function destroy(JournalEntry $journalEntry): RedirectResponse
    {
        $this->service->deleteEntry($journalEntry);

        return redirect()->route('journal-entries.index')->with('success', __('erp.accounting.deleted'));
    }

    public function trialBalance(Request $request): Response
    {
        $rows = $this->service->trialBalance($request->only(['date_from', 'date_to']));

        return Inertia::render('Accounting/TrialBalance', [
            'rows' => $rows,
            'filters' => $request->only(['date_from', 'date_to']),
        ]);
    }
}

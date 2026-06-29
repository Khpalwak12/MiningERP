<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\PersonalLedgerTransaction\StorePersonalLedgerTransactionRequest;
use App\Http\Requests\PersonalLedgerTransaction\UpdatePersonalLedgerTransactionRequest;
use App\Http\Resources\PersonalLedgerTransactionResource;
use App\Models\PersonalContact;
use App\Models\PersonalLedgerTransaction;
use App\Services\PersonalLedgerTransactionService;
use App\Support\ActiveFinancialYear;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PersonalLedgerTransactionController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private PersonalLedgerTransactionService $service)
    {
        $this->registerModulePermissions('personal-accounts');
    }

    public function create(PersonalContact $contact): Response|RedirectResponse
    {
        if (ActiveFinancialYear::isAllYearsMode()) {
            return redirect()
                ->route('personal-accounts.contacts.show', $contact)
                ->with('error', __('erp.financial_years.all_years_read_only'));
        }

        if (! ActiveFinancialYear::activeYear()) {
            return redirect()
                ->route('personal-accounts.contacts.show', $contact)
                ->with('error', __('erp.financial_years.no_active_year'));
        }

        return Inertia::render('PersonalAccounts/Transactions/Create', [
            'contact' => $contact->only(['id', 'name']),
        ]);
    }

    public function store(StorePersonalLedgerTransactionRequest $request, PersonalContact $contact): RedirectResponse
    {
        $this->service->create(array_merge($request->validated(), [
            'personal_contact_id' => $contact->id,
        ]));

        return redirect()->route('personal-accounts.contacts.show', $contact)
            ->with('success', __('erp.personal_accounts.transaction_created'));
    }

    public function edit(PersonalLedgerTransaction $transaction): Response
    {
        $transaction->load('contact');

        return Inertia::render('PersonalAccounts/Transactions/Edit', [
            'transaction' => new PersonalLedgerTransactionResource($transaction),
        ]);
    }

    public function update(UpdatePersonalLedgerTransactionRequest $request, PersonalLedgerTransaction $transaction): RedirectResponse
    {
        $this->service->update($transaction, $request->validated());

        return redirect()->route('personal-accounts.contacts.show', $transaction->personal_contact_id)
            ->with('success', __('erp.personal_accounts.transaction_updated'));
    }

    public function destroy(PersonalLedgerTransaction $transaction): RedirectResponse
    {
        $contactId = $transaction->personal_contact_id;
        $this->service->delete($transaction);

        return redirect()->route('personal-accounts.contacts.show', $contactId)
            ->with('success', __('erp.personal_accounts.transaction_deleted'));
    }
}

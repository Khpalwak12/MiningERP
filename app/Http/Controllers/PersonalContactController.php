<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\PersonalContact\StorePersonalContactRequest;
use App\Http\Requests\PersonalContact\UpdatePersonalContactRequest;
use App\Http\Resources\PersonalContactResource;
use App\Models\PersonalContact;
use App\Services\PersonalContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonalContactController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private PersonalContactService $service)
    {
        $this->registerModulePermissions('personal-accounts');
    }

    public function index(Request $request): Response
    {
        $contacts = $this->service->paginate($request->only(['search', 'status', 'contact_type']));

        return Inertia::render('PersonalAccounts/Contacts/Index', [
            'contacts' => PersonalContactResource::collection($contacts),
            'filters' => $request->only(['search', 'status', 'contact_type']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PersonalAccounts/Contacts/Create');
    }

    public function store(StorePersonalContactRequest $request): RedirectResponse
    {
        $contact = $this->service->create($request->validated());

        return redirect()->route('personal-accounts.contacts.show', $contact)
            ->with('success', __('erp.personal_accounts.contact_created'));
    }

    public function show(PersonalContact $contact): Response
    {
        return Inertia::render('PersonalAccounts/Contacts/Show', [
            'contact' => new PersonalContactResource($contact),
            'balances' => $this->service->balances($contact),
            'ledger' => $this->service->ledger($contact),
        ]);
    }

    public function edit(PersonalContact $contact): Response
    {
        return Inertia::render('PersonalAccounts/Contacts/Edit', [
            'contact' => new PersonalContactResource($contact),
        ]);
    }

    public function update(UpdatePersonalContactRequest $request, PersonalContact $contact): RedirectResponse
    {
        $this->service->update($contact, $request->validated());

        return redirect()->route('personal-accounts.contacts.show', $contact)
            ->with('success', __('erp.personal_accounts.contact_updated'));
    }

    public function destroy(PersonalContact $contact): RedirectResponse
    {
        $this->service->delete($contact);

        return redirect()->route('personal-accounts.contacts.index')
            ->with('success', __('erp.personal_accounts.contact_deleted'));
    }
}

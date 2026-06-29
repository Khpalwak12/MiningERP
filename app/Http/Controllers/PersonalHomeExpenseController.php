<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\PersonalHomeExpense\StorePersonalHomeExpenseRequest;
use App\Http\Requests\PersonalHomeExpense\UpdatePersonalHomeExpenseRequest;
use App\Http\Resources\PersonalHomeExpenseResource;
use App\Models\PersonalHomeExpense;
use App\Services\PersonalHomeExpenseService;
use App\Support\ActiveFinancialYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonalHomeExpenseController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private PersonalHomeExpenseService $service)
    {
        $this->registerModulePermissions('personal-accounts');
    }

    public function index(Request $request): Response
    {
        $expenses = $this->service->paginate($request->only(['search', 'date_from', 'date_to']));

        return Inertia::render('PersonalAccounts/HomeExpenses/Index', [
            'expenses' => PersonalHomeExpenseResource::collection($expenses),
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        if (ActiveFinancialYear::isAllYearsMode()) {
            return redirect()
                ->route('personal-accounts.home-expenses.index')
                ->with('error', __('erp.financial_years.all_years_read_only'));
        }

        if (! ActiveFinancialYear::activeYear()) {
            return redirect()
                ->route('personal-accounts.home-expenses.index')
                ->with('error', __('erp.financial_years.no_active_year'));
        }

        return Inertia::render('PersonalAccounts/HomeExpenses/Create');
    }

    public function store(StorePersonalHomeExpenseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('personal-accounts.home-expenses.index')
            ->with('success', __('erp.personal_accounts.home_expense_created'));
    }

    public function edit(PersonalHomeExpense $homeExpense): Response
    {
        return Inertia::render('PersonalAccounts/HomeExpenses/Edit', [
            'expense' => new PersonalHomeExpenseResource($homeExpense),
        ]);
    }

    public function update(UpdatePersonalHomeExpenseRequest $request, PersonalHomeExpense $homeExpense): RedirectResponse
    {
        $this->service->update($homeExpense, $request->validated());

        return redirect()->route('personal-accounts.home-expenses.index')
            ->with('success', __('erp.personal_accounts.home_expense_updated'));
    }

    public function destroy(PersonalHomeExpense $homeExpense): RedirectResponse
    {
        $this->service->delete($homeExpense);

        return redirect()->route('personal-accounts.home-expenses.index')
            ->with('success', __('erp.personal_accounts.home_expense_deleted'));
    }
}

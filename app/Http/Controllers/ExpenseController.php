<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private ExpenseService $service)
    {
        $this->registerModulePermissions('expenses');
    }

    public function index(Request $request): Response
    {
        $expenses = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'expense_category_id', 'subcategory']));

        return Inertia::render('Expenses/Index', [
            'expenses' => ExpenseResource::collection($expenses),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'expense_category_id', 'subcategory']),
            'categories' => ExpenseCategoryResource::collection(ExpenseCategory::listForSelect()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Expenses/Create', [
            'categories' => ExpenseCategoryResource::collection(ExpenseCategory::listForSelect()),
        ]);
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('expenses.index')->with('success', __('erp.expenses.created'));
    }

    public function show(Expense $expense): Response
    {
        $expense->load(['category', 'creator']);

        return Inertia::render('Expenses/Show', [
            'expense' => new ExpenseResource($expense),
        ]);
    }

    public function edit(Expense $expense): Response
    {
        $expense->load(['category']);

        return Inertia::render('Expenses/Edit', [
            'expense' => new ExpenseResource($expense),
            'categories' => ExpenseCategoryResource::collection(ExpenseCategory::listForSelect()),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->service->update($expense, $request->validated());

        return redirect()->route('expenses.index')->with('success', __('erp.expenses.updated'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->service->delete($expense);

        return redirect()->route('expenses.index')->with('success', __('erp.expenses.deleted'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\ExpenseCategory\StoreExpenseCategoryRequest;
use App\Http\Requests\ExpenseCategory\UpdateExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseCategoryController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private ExpenseCategoryService $service)
    {
        $this->registerModulePermissions('expense-categories');
    }

    public function index(Request $request): Response
    {
        $categories = $this->service->paginate($request->only(['search']));

        return Inertia::render('ExpenseCategories/Index', [
            'categories' => ExpenseCategoryResource::collection($categories),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ExpenseCategories/Create');
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('expense-categories.index')
            ->with('success', __('erp.expense_categories.created'));
    }

    public function edit(ExpenseCategory $expenseCategory): Response
    {
        return Inertia::render('ExpenseCategories/Edit', [
            'category' => new ExpenseCategoryResource($expenseCategory),
        ]);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $this->service->update($expenseCategory, $request->validated());

        return redirect()->route('expense-categories.index')
            ->with('success', __('erp.expense_categories.updated'));
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        try {
            $this->service->delete($expenseCategory);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('expense-categories.index')->with('error', $e->getMessage());
        }

        return redirect()->route('expense-categories.index')
            ->with('success', __('erp.expense_categories.deleted'));
    }
}

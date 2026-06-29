<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_expense_category_crud_and_localized_display(): void
    {
        $this->actingAs($this->admin)->get(route('expense-categories.index'))->assertOk();

        $this->actingAs($this->admin)->post(route('expense-categories.store'), [
            'name_en' => 'Office Supplies',
            'name_ps' => 'د دفتر توکي',
            'description' => 'Stationery and office items',
        ])->assertRedirect(route('expense-categories.index'));

        $category = ExpenseCategory::query()->where('name_en', 'Office Supplies')->firstOrFail();

        $this->admin->update(['locale' => 'ps']);
        $psResponse = $this->actingAs($this->admin)->get(route('expenses.create'));
        $psResponse->assertOk();
        $psNames = collect($psResponse->original->getData()['page']['props']['categories']['data'])->pluck('name');
        $this->assertTrue($psNames->contains('د دفتر توکي'));

        $this->admin->update(['locale' => 'en']);
        $enResponse = $this->actingAs($this->admin)->get(route('expenses.create'));
        $enNames = collect($enResponse->original->getData()['page']['props']['categories']['data'])->pluck('name');
        $this->assertTrue($enNames->contains('Office Supplies'));

        $this->actingAs($this->admin)->put(route('expense-categories.update', $category), [
            'name_en' => 'Office Items',
            'name_ps' => 'د دفتر توکي',
            'description' => 'Updated',
        ])->assertRedirect(route('expense-categories.index'));

        $this->assertSame('Office Items', $category->fresh()->name_en);
    }

    public function test_cannot_delete_category_in_use(): void
    {
        $category = ExpenseCategory::query()->where('slug', 'kitchen')->firstOrFail();
        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();

        Expense::query()->create([
            'financial_year_id' => $year->id,
            'expense_category_id' => $category->id,
            'expense_date' => now(),
            'amount' => 100,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('expense-categories.destroy', $category))
            ->assertRedirect(route('expense-categories.index'));

        $this->assertNotNull($category->fresh());
    }
}

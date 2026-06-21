<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Support\JalaliDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseLocalizationAndDateSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_expense_categories_are_localized_for_pashto_users(): void
    {
        $this->admin->update(['locale' => 'ps']);

        $response = $this->actingAs($this->admin)->get(route('expenses.index'));

        $response->assertOk();
        $categories = $response->original->getData()['page']['props']['categories']['data'];

        $names = collect($categories)->pluck('name')->all();

        $this->assertContains('اشپزخانه', $names);
        $this->assertContains('روغنیات او تیل', $names);
        $this->assertNotContains('Kitchen', $names);
    }

    public function test_expense_categories_remain_english_for_english_users(): void
    {
        $this->admin->update(['locale' => 'en']);

        $response = $this->actingAs($this->admin)->get(route('expenses.index'));

        $response->assertOk();
        $categories = $response->original->getData()['page']['props']['categories']['data'];

        $names = collect($categories)->pluck('name')->all();

        $this->assertContains('Kitchen', $names);
        $this->assertNotContains('اشپزخانه', $names);
    }

    public function test_expense_list_can_be_filtered_by_shamsi_date_range(): void
    {
        $category = ExpenseCategory::query()->where('slug', 'kitchen')->firstOrFail();

        $inRangeDate = JalaliDate::toGregorian('1403/01/15');
        $outOfRangeDate = JalaliDate::toGregorian('1403/02/15');

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => $inRangeDate,
            'amount' => 100,
            'description' => 'In range',
            'created_by' => $this->admin->id,
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => $outOfRangeDate,
            'amount' => 200,
            'description' => 'Out of range',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.index', [
            'date_from' => '1403/01/01',
            'date_to' => '1403/01/31',
        ]));

        $response->assertOk();
        $expenses = $response->original->getData()['page']['props']['expenses']['data'];

        $this->assertCount(1, $expenses);
        $this->assertSame('In range', $expenses[0]['description']);
    }

    public function test_expense_search_supports_single_shamsi_date(): void
    {
        $category = ExpenseCategory::query()->where('slug', 'kitchen')->firstOrFail();
        $targetDate = JalaliDate::toGregorian('1403/03/01');

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => $targetDate,
            'amount' => 50,
            'description' => 'Target date expense',
            'created_by' => $this->admin->id,
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => JalaliDate::toGregorian('1403/03/02'),
            'amount' => 75,
            'description' => 'Other date expense',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.index', [
            'search' => '1403/03/01',
        ]));

        $response->assertOk();
        $expenses = $response->original->getData()['page']['props']['expenses']['data'];

        $this->assertCount(1, $expenses);
        $this->assertSame('Target date expense', $expenses[0]['description']);
    }

    public function test_expense_search_supports_shamsi_date_range(): void
    {
        $category = ExpenseCategory::query()->where('slug', 'kitchen')->firstOrFail();

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => JalaliDate::toGregorian('1405/03/01'),
            'amount' => 10,
            'description' => 'Range start',
            'created_by' => $this->admin->id,
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => JalaliDate::toGregorian('1405/03/31'),
            'amount' => 20,
            'description' => 'Range end',
            'created_by' => $this->admin->id,
        ]);

        Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => JalaliDate::toGregorian('1405/04/01'),
            'amount' => 30,
            'description' => 'Outside range',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('expenses.index', [
            'search' => '1405/03/01 - 1405/03/31',
        ]));

        $response->assertOk();
        $expenses = $response->original->getData()['page']['props']['expenses']['data'];

        $this->assertCount(2, $expenses);
        $this->assertEqualsCanonicalizing(
            ['Range start', 'Range end'],
            collect($expenses)->pluck('description')->all()
        );
    }
}

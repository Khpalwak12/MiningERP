<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\PersonalContact;
use App\Models\User;
use App\Services\PersonalContactService;
use App\Services\PersonalLedgerTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_ledger_tracks_credit_payment_and_balance_per_currency(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $contact = PersonalContact::query()->create([
            'name' => 'دوکاندار احمد',
            'contact_type' => 'shopkeeper',
            'status' => 'active',
        ]);

        $txService = app(PersonalLedgerTransactionService::class);

        $txService->create([
            'financial_year_id' => $year->id,
            'personal_contact_id' => $contact->id,
            'transaction_date' => now(),
            'transaction_type' => 'credit',
            'currency' => 'AFN',
            'amount' => 10000,
            'description' => 'سودا په قرض',
            'created_by' => $user->id,
        ]);

        $txService->create([
            'financial_year_id' => $year->id,
            'personal_contact_id' => $contact->id,
            'transaction_date' => now(),
            'transaction_type' => 'payment',
            'currency' => 'AFN',
            'amount' => 3000,
            'description' => 'جزوي تادیه',
            'created_by' => $user->id,
        ]);

        $txService->create([
            'financial_year_id' => $year->id,
            'personal_contact_id' => $contact->id,
            'transaction_date' => now(),
            'transaction_type' => 'credit',
            'currency' => 'USD',
            'amount' => 500,
            'created_by' => $user->id,
        ]);

        $balances = app(PersonalContactService::class)->balances($contact->fresh());

        $this->assertSame(7000.0, $balances['AFN']);
        $this->assertSame(500.0, $balances['USD']);
    }

    public function test_personal_accounts_routes_are_accessible(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $this->actingAs($user)->get(route('personal-accounts.contacts.index'))->assertOk();
        $this->actingAs($user)->get(route('personal-accounts.home-expenses.index'))->assertOk();
    }
}

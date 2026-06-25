<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\FinancialYear;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsReportFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_number_filter_uses_partial_matching(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $customer = Customer::factory()->create(['status' => 'active']);
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        CustomerPayment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => $customer->id,
            'payment_date' => now(),
            'receipt_number' => 'REC-123',
            'amount' => 1000,
            'received_by' => 'Receiver',
            'created_by' => $user->id,
        ]);

        CustomerPayment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => $customer->id,
            'payment_date' => now(),
            'receipt_number' => 'PAY-999',
            'amount' => 2000,
            'received_by' => 'Receiver',
            'created_by' => $user->id,
        ]);

        CustomerPayment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => $customer->id,
            'payment_date' => now(),
            'receipt_number' => 'INV-123-A',
            'amount' => 3000,
            'received_by' => 'Receiver',
            'created_by' => $user->id,
        ]);

        $results = app(ReportService::class)->paymentsReport([
            'filter_by' => 'receipt_number',
            'filter_value' => '123',
        ]);

        $this->assertCount(2, $results);
        $this->assertTrue($results->every(fn ($payment) => str_contains($payment->receipt_number, '123')));
    }

    public function test_customer_advanced_filter_uses_exact_matching(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $factoryA = Customer::factory()->create(['name' => 'Factory A', 'status' => 'active']);
        $factoryB = Customer::factory()->create(['name' => 'Factory B', 'status' => 'active']);
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        CustomerPayment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => $factoryA->id,
            'payment_date' => now(),
            'receipt_number' => 'REC-123',
            'amount' => 1000,
            'received_by' => 'Receiver',
            'created_by' => $user->id,
        ]);

        CustomerPayment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => $factoryB->id,
            'payment_date' => now(),
            'receipt_number' => 'REC-123-B',
            'amount' => 2000,
            'received_by' => 'Receiver',
            'created_by' => $user->id,
        ]);

        $results = app(ReportService::class)->paymentsReport([
            'filter_by' => 'customer',
            'filter_value' => $factoryA->id,
        ]);

        $this->assertCount(1, $results);
        $this->assertSame($factoryA->id, $results->first()->customer_id);
        $this->assertSame('REC-123', $results->first()->receipt_number);
    }
}

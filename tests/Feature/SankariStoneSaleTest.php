<?php

namespace Tests\Feature;

use App\Models\SankariStoneSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SankariStoneSaleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_total_amount_is_subtotal_minus_discount(): void
    {
        $sale = SankariStoneSale::query()->create([
            'sale_date' => now(),
            'truck_count' => 10,
            'price_per_truck' => 600,
            'discount' => 500,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(6000.0, $sale->subtotal());
        $this->assertEquals(500.0, (float) $sale->discount);
        $this->assertEquals(5500.0, (float) $sale->total_amount);
    }

    public function test_store_rejects_discount_greater_than_subtotal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('sankari.store'), [
            'sale_date' => '1404/01/01',
            'truck_count' => 10,
            'price_per_truck' => 600,
            'discount' => 6001,
        ]);

        $response->assertSessionHasErrors('discount');
    }

    public function test_store_creates_sale_with_discount(): void
    {
        $response = $this->actingAs($this->admin)->post(route('sankari.store'), [
            'sale_date' => '1404/01/01',
            'truck_count' => 10,
            'price_per_truck' => 600,
            'discount' => 500,
        ]);

        $response->assertRedirect(route('sankari.index'));

        $sale = SankariStoneSale::query()->latest('id')->first();
        $this->assertNotNull($sale);
        $this->assertEquals(500.0, (float) $sale->discount);
        $this->assertEquals(5500.0, (float) $sale->total_amount);
    }
}

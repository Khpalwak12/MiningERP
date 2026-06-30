<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MarbleShipment;
use App\Models\MineType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersianDigitInputTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipment_store_accepts_persian_digits_in_date_and_amounts(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $customer = Customer::query()->firstOrFail();
        $mineType = MineType::query()->firstOrFail();

        $response = $this->actingAs($user)->post(route('shipments.store'), [
            'shipment_date' => '۱۴۰۴/۰۱/۱۵',
            'customer_id' => $customer->id,
            'mine_type_id' => $mineType->id,
            'quantity_ton' => '۱۰',
            'price_per_ton' => '۱۵۰۰',
        ]);

        $response->assertRedirect(route('shipments.index'));

        $shipment = MarbleShipment::query()->latest('id')->firstOrFail();
        $this->assertEquals(10.0, (float) $shipment->quantity_ton);
        $this->assertEquals(1500.0, (float) $shipment->price_per_ton);
        $this->assertEquals(15000.0, (float) $shipment->total_amount);
    }
}

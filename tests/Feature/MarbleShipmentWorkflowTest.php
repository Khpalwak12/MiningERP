<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MarbleShipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarbleShipmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_shipment_can_be_created_without_weight_and_price(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $customer = Customer::query()->firstOrFail();
        $mineType = \App\Models\MineType::query()->firstOrFail();
        $stoneType = \App\Models\StoneType::query()->firstOrFail();

        $response = $this->actingAs($user)->post(route('shipments.store'), [
            'shipment_date' => '1404/01/15',
            'customer_id' => $customer->id,
            'mine_type_id' => $mineType->id,
            'stone_type_id' => $stoneType->id,
            'driver_name' => 'Test Driver',
        ]);

        $response->assertRedirect(route('shipments.index'));

        $shipment = MarbleShipment::query()->latest('id')->firstOrFail();
        $this->assertNull($shipment->quantity_ton);
        $this->assertNull($shipment->price_per_ton);
        $this->assertNull($shipment->total_amount);
        $this->assertSame(MarbleShipment::STATUS_PENDING_BOTH, $shipment->status);
    }

    public function test_completing_shipment_recalculates_total_and_status(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $customer = Customer::query()->firstOrFail();

        $shipment = MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'shipment_date' => now(),
            'quantity_ton' => null,
            'price_per_ton' => null,
            'total_amount' => null,
            'status' => MarbleShipment::STATUS_PENDING_BOTH,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('shipments.update', $shipment), [
            'quantity_ton' => 10,
            'price_per_ton' => 1500,
        ]);

        $response->assertRedirect(route('shipments.index'));

        $shipment->refresh();
        $this->assertSame(MarbleShipment::STATUS_COMPLETED, $shipment->status);
        $this->assertEquals(15000.0, (float) $shipment->total_amount);
    }

    public function test_pending_shipments_do_not_affect_customer_balance(): void
    {
        $customer = Customer::query()->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'shipment_date' => now(),
            'quantity_ton' => 20,
            'price_per_ton' => null,
            'created_by' => $user->id,
        ]);

        MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'shipment_date' => now(),
            'quantity_ton' => 5,
            'price_per_ton' => 1000,
            'created_by' => $user->id,
        ]);

        $this->assertEquals(5000.0, $customer->fresh()->total_sales);
    }

    public function test_existing_shipments_are_marked_completed_by_migration_default(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $customer = Customer::query()->firstOrFail();

        $shipment = MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'shipment_date' => now()->subDay(),
            'quantity_ton' => 12.5,
            'price_per_ton' => 800,
            'created_by' => $user->id,
        ]);

        $this->assertSame(MarbleShipment::STATUS_COMPLETED, $shipment->status);
        $this->assertEquals(10000.0, (float) $shipment->total_amount);
    }
}

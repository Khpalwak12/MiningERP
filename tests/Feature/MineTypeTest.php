<?php

namespace Tests\Feature;

use App\Models\MarbleShipment;
use App\Models\MineType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MineTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_mine_type_crud_and_localized_display(): void
    {
        $this->actingAs($this->admin)->get(route('mine-types.index'))->assertOk();

        $this->actingAs($this->admin)->post(route('mine-types.store'), [
            'name_en' => 'Blue Marble',
            'name_ps' => 'شین مرمر',
            'description' => 'Blue variety',
        ])->assertRedirect(route('mine-types.index'));

        $mineType = MineType::query()->where('name_en', 'Blue Marble')->firstOrFail();

        $this->admin->update(['locale' => 'ps']);
        $psResponse = $this->actingAs($this->admin)->get(route('shipments.create'));
        $psResponse->assertOk();
        $psNames = collect($psResponse->original->getData()['page']['props']['mineTypes']['data'])->pluck('name');
        $this->assertTrue($psNames->contains('شین مرمر'));

        $this->admin->update(['locale' => 'en']);
        $enResponse = $this->actingAs($this->admin)->get(route('shipments.create'));
        $enNames = collect($enResponse->original->getData()['page']['props']['mineTypes']['data'])->pluck('name');
        $this->assertTrue($enNames->contains('Blue Marble'));

        $this->actingAs($this->admin)->put(route('mine-types.update', $mineType), [
            'name_en' => 'Blue Stone',
            'name_ps' => 'شین مرمر',
            'description' => 'Updated',
        ])->assertRedirect(route('mine-types.index'));

        $this->assertSame('Blue Stone', $mineType->fresh()->name_en);
    }

    public function test_cannot_delete_mine_type_in_use(): void
    {
        $mineType = MineType::query()->where('slug', 'white-marble')->firstOrFail();
        $customer = \App\Models\Customer::query()->firstOrFail();

        MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'mine_type_id' => $mineType->id,
            'shipment_date' => now(),
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('mine-types.destroy', $mineType))
            ->assertRedirect(route('mine-types.index'));

        $this->assertNotNull($mineType->fresh());
    }
}

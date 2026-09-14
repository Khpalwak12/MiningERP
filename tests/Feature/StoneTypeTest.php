<?php

namespace Tests\Feature;

use App\Models\MarbleShipment;
use App\Models\StoneType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoneTypeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_stone_type_crud_and_localized_display(): void
    {
        $this->actingAs($this->admin)->get(route('stone-types.index'))->assertOk();

        $this->actingAs($this->admin)->post(route('stone-types.store'), [
            'name_en' => 'Polished',
            'name_ps' => 'پالش شوی',
            'description' => 'Polished stone',
        ])->assertRedirect(route('stone-types.index'));

        $stoneType = StoneType::query()->where('name_en', 'Polished')->firstOrFail();

        $this->admin->update(['locale' => 'ps']);
        $psResponse = $this->actingAs($this->admin)->get(route('shipments.create'));
        $psResponse->assertOk();
        $psNames = collect($psResponse->original->getData()['page']['props']['stoneTypes']['data'])->pluck('name');
        $this->assertTrue($psNames->contains('پالش شوی'));

        $this->admin->update(['locale' => 'en']);
        $enResponse = $this->actingAs($this->admin)->get(route('shipments.create'));
        $enNames = collect($enResponse->original->getData()['page']['props']['stoneTypes']['data'])->pluck('name');
        $this->assertTrue($enNames->contains('Polished'));

        $this->actingAs($this->admin)->put(route('stone-types.update', $stoneType), [
            'name_en' => 'Polished Stone',
            'name_ps' => 'پالش شوی',
            'description' => 'Updated',
        ])->assertRedirect(route('stone-types.index'));

        $this->assertSame('Polished Stone', $stoneType->fresh()->name_en);
    }

    public function test_cannot_delete_stone_type_in_use(): void
    {
        $stoneType = StoneType::query()->where('slug', 'block')->firstOrFail();
        $customer = \App\Models\Customer::query()->firstOrFail();

        MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'stone_type_id' => $stoneType->id,
            'shipment_date' => now(),
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('stone-types.destroy', $stoneType))
            ->assertRedirect(route('stone-types.index'));

        $this->assertNotNull($stoneType->fresh());
    }
}

<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\MineAsset;
use App\Models\User;
use App\Services\MineAssetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MineAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_mine_asset_with_registration_date(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $asset = app(MineAssetService::class)->create([
            'financial_year_id' => $year->id,
            'name' => 'چوکی الماری',
            'related_to' => 'دفتر',
            'quantity' => 5,
            'unit' => 'piece',
            'status' => MineAsset::STATUS_USABLE,
            'registration_date' => now(),
            'created_by' => $user->id,
        ]);

        $this->assertSame('چوکی الماری', $asset->name);
        $this->assertSame('دفتر', $asset->related_to);
        $this->assertTrue($asset->isUsable());
    }

    public function test_mine_assets_index_route_is_accessible(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $this->actingAs($user)
            ->get(route('mine-assets.index'))
            ->assertOk();
    }
}

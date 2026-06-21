<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RtlLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_html_renders_rtl_for_pashto_locale(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $user->update(['locale' => 'ps']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }

    public function test_html_renders_ltr_for_english_locale(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $user->update(['locale' => 'en']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dir="ltr"', false);
    }
}

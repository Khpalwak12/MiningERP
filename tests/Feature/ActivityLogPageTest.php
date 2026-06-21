<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_activity_logs_page_returns_valid_inertia_props(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        activity()->causedBy($user)->log('Test activity entry');

        $response = $this->actingAs($user)->get(route('activity-logs.index'));

        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page->component('ActivityLogs/Index'));

        $page = $response->original->getData()['page'];
        $logs = $page['props']['logs'];

        $this->assertIsArray($logs['data']);
        $this->assertIsArray($logs['meta']['links']);

        if (count($logs['data']) > 0) {
            $first = $logs['data'][0];
            $this->assertArrayHasKey('description', $first);
            $this->assertArrayHasKey('created_at_shamsi', $first);
            $this->assertArrayHasKey('causer_name', $first);
            $this->assertIsString($first['description']);
        }
    }
}

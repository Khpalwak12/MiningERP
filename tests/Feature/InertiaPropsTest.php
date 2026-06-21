<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InertiaPropsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_paginated_resource_props_expose_meta_links_array(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $response = $this->actingAs($user)->get(route('customers.index'));

        $response->assertSuccessful();

        $page = $response->original->getData()['page'];
        $customers = $page['props']['customers'];

        $this->assertIsArray($customers['data']);
        $this->assertIsArray($customers['meta']['links']);
    }
}

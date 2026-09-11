<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Table;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KioskTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function kiosk_can_be_accessed_via_tenant_slug()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Kedai Robby',
            'slug' => 'kedairobby-id'
        ]);

        $response = $this->get('/kiosk/kedairobby-id');

        $response->assertStatus(200);
        $response->assertSee('Kiosk');
    }
}

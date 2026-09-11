<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Setting;
use App\Models\Table;
use App\Models\Category;
use App\Livewire\SuperAdmin\Tenants;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdminTenantsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function super_admin_can_create_new_restaurant_with_owner_and_starters()
    {
        $superAdmin = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
            'tenant_id' => null,
        ]);

        Livewire::actingAs($superAdmin)
            ->test(Tenants::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('name', 'Kedai Kopi Senja')
            ->assertSet('slug', 'kedai-kopi-senja')
            ->set('address', 'Jl. Senja No. 10')
            ->set('phone', '081298765432')
            ->set('ownerName', 'Budi Santoso')
            ->set('ownerEmail', 'budi@kopisenja.com')
            ->set('ownerPassword', 'password123')
            ->call('createTenant')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Kedai Kopi Senja',
            'slug' => 'kedai-kopi-senja',
            'phone' => '081298765432',
        ]);

        $tenant = Tenant::where('slug', 'kedai-kopi-senja')->first();

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@kopisenja.com',
            'tenant_id' => $tenant->id,
            'role' => \App\Enums\UserRole::OWNER->value,
        ]);

        // Verify starter settings
        $this->assertDatabaseHas('settings', [
            'tenant_id' => $tenant->id,
            'key' => 'store_name',
            'value' => 'Kedai Kopi Senja',
        ]);

        // Verify starter tables
        $this->assertEquals(6, Table::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->count());

        // Verify starter categories
        $this->assertEquals(3, Category::withoutGlobalScope('tenant')->where('tenant_id', $tenant->id)->count());
    }
}

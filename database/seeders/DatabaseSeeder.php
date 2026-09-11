<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Table;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Setting;
use App\Enums\UserRole;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT TENANT DEFAULT (RESTORAN)
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'kedairobby-id'],
            [
                'name' => 'KedaiRobby.id',
                'address' => 'Jl. Kuliner Digital No. 123',
                'phone' => '08123456789',
                'is_active' => true,
            ]
        );
        $this->command->info('✅ Restoran Default berhasil dibuat!');

        // 2. BUAT USER SUPER ADMIN (Platform)
        User::updateOrCreate(
            ['email' => 'superadmin@laracarte.com'],
            [
                'name' => 'Super Admin KedaiRobby.id',
                'password' => bcrypt('password123'),
                'role' => UserRole::SUPER_ADMIN,
                'tenant_id' => null,
            ]
        );
        $this->command->info('✅ User Super Admin berhasil dibuat! (superadmin@laracarte.com / password123)');

        // 3. BUAT USER OWNER / ADMIN RESTORAN
        User::updateOrCreate(
            ['email' => 'admin@laracarte.com'],
            [
                'name' => 'Admin KedaiRobby.id',
                'password' => bcrypt('password123'),
                'role' => UserRole::OWNER,
                'tenant_id' => $tenant->id,
            ]
        );
        $this->command->info('✅ User Admin Resto berhasil dibuat! (admin@laracarte.com / password123)');

        // 4. BUAT PENGATURAN AWAL (SETTINGS)
        $defaultSettings = [
            ['key' => 'store_name', 'value' => 'KedaiRobby.id', 'description' => 'Nama Toko/Restoran'],
            ['key' => 'store_address', 'value' => 'Jl. Kuliner Digital No. 123', 'description' => 'Alamat Restoran'],
            ['key' => 'tax_rate', 'value' => '11', 'description' => 'Pajak PB1 (%)'],
            ['key' => 'service_charge', 'value' => '5', 'description' => 'Service Charge (%)'],
            ['key' => 'printer_name', 'value' => 'Thermal_Printer', 'description' => 'Nama Printer Kasir'],
            ['key' => 'loyalty_enabled', 'value' => '1', 'description' => 'Status Fitur Poin Member'],
            ['key' => 'point_earn_rate', 'value' => '10000', 'description' => 'Belanja per Poin'],
            ['key' => 'point_redeem_value', 'value' => '100', 'description' => 'Nilai per 1 Poin (Rp)'],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'tenant_id' => $tenant->id],
                ['value' => $setting['value'], 'description' => $setting['description']]
            );
        }
        $this->command->info('✅ Pengaturan Resto berhasil diinisialisasi!');

        // 5. BUAT DATA MEJA (Agar link QR tidak Not Found)
        $tables = ['Meja 1', 'Meja 2', 'Meja 3', 'Meja 4', 'Meja 5', 'Meja VIP'];
        foreach ($tables as $tableName) {
            Table::updateOrCreate(
                ['slug' => Str::slug($tableName), 'tenant_id' => $tenant->id],
                [
                    'name' => $tableName,
                    'status' => 'empty'
                ]
            );
        }
        $this->command->info('✅ 6 Meja berhasil dibuat!');

        // 6. BUAT KATEGORI
        $catMakanan = Category::updateOrCreate(
            ['slug' => 'makanan-berat', 'tenant_id' => $tenant->id],
            ['name' => 'Makanan Berat']
        );
        $catMinuman = Category::updateOrCreate(
            ['slug' => 'minuman', 'tenant_id' => $tenant->id],
            ['name' => 'Minuman']
        );
        $catSnack = Category::updateOrCreate(
            ['slug' => 'cemilan', 'tenant_id' => $tenant->id],
            ['name' => 'Cemilan']
        );

        // 7. BUAT PRODUK/MENU DUMMY
        $products = [
            [
                'category_id' => $catMakanan->id,
                'name' => 'Nasi Goreng Spesial',
                'description' => 'Nasi goreng dengan telur, ayam suwir, dan kerupuk udang.',
                'price' => 25000,
                'image' => null,
                'stock' => 50,
                'is_available' => true,
                'tenant_id' => $tenant->id,
            ],
            [
                'category_id' => $catMakanan->id,
                'name' => 'Ayam Bakar Madu',
                'description' => 'Ayam bakar dengan olesan madu murni + lalapan.',
                'price' => 30000,
                'image' => null,
                'stock' => 30,
                'is_available' => true,
                'tenant_id' => $tenant->id,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Teh melati asli dengan gula batu.',
                'price' => 5000,
                'image' => null,
                'stock' => 100,
                'is_available' => true,
                'tenant_id' => $tenant->id,
            ],
            [
                'category_id' => $catMinuman->id,
                'name' => 'Kopi Susu Gula Aren',
                'description' => 'Kopi house blend dengan susu fresh milk.',
                'price' => 18000,
                'image' => null,
                'stock' => 50,
                'is_available' => true,
                'tenant_id' => $tenant->id,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Kentang Goreng',
                'description' => 'French fries renyah dengan saus sambal.',
                'price' => 15000,
                'image' => null,
                'stock' => 40,
                'is_available' => true,
                'tenant_id' => $tenant->id,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name'], 'tenant_id' => $tenant->id],
                $product
            );
        }
        $this->command->info('✅ 5 Menu Makanan berhasil dibuat!');
    }
}

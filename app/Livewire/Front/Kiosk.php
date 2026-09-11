<?php

namespace App\Livewire\Front;

use App\Models\Table;
use App\Models\Tenant;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

// Kiosk mewarisi OrderPage untuk Cart & Checkout Logic, tapi kita override mount & render
class Kiosk extends OrderPage
{
    public function mount($slug)
    {
        // 1. Cek apakah slug adalah slug Tenant / Restoran (misal: kedairobby-id)
        $tenant = Tenant::where('slug', $slug)->first();

        if ($tenant) {
            // Ambil atau buat meja virtual khusus Kiosk untuk restoran ini
            $this->table = Table::withoutGlobalScope('tenant')->firstOrCreate(
                [
                    'slug' => 'kiosk-' . $tenant->slug,
                    'tenant_id' => $tenant->id,
                ],
                [
                    'name' => 'Kiosk Mandiri',
                    'status' => 'empty',
                ]
            );
        } else {
            // 2. Jika bukan slug tenant, coba cari berdasarkan slug Meja
            $this->table = Table::withoutGlobalScope('tenant')->where('slug', $slug)->first();

            if (!$this->table) {
                abort(404, 'Restoran atau Meja Kiosk tidak ditemukan.');
            }
        }

        $this->table_name = $this->table->name;
        session()->put('table_id', $this->table->id);
        session()->put('tenant_id', $this->table->tenant_id);

        $this->taxRate = (int) Setting::value('tax_rate', 11);
        $this->serviceRate = (int) Setting::value('service_charge', 5);

        $this->loyaltyEnabled = (bool) Setting::value('loyalty_enabled', 1);
        $this->pointEarnRate = (int) Setting::value('point_earn_rate', 10000);
        $this->pointRedeemValue = (int) Setting::value('point_redeem_value', 100);

        $this->bankName = Setting::value('bank_name', 'Bank BCA');
        $this->bankAccountNumber = Setting::value('bank_account_number', '123 456 7890');
        $this->bankAccountName = Setting::value('bank_account_name', 'a.n. Kedai Robby');
        $this->qrisImage = Setting::value('qris_image', null);

        // Pastikan activeCategory default
        $this->activeCategory = 'all';
    }

    public function render()
    {
        $tenantId = $this->table->tenant_id ?? null;

        $categories = Category::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->get();
        
        $query = Product::query()
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->with(['variants'])
            ->where('is_available', true);

        if ($this->activeCategory !== 'all') {
            $query->where('category_id', $this->activeCategory);
        }

        $products = $query->orderBy('name')->get();

        return view('livewire.front.kiosk', [
            'categories' => $categories,
            'products' => $products
        ])->layout('layouts.kiosk');
    }
}

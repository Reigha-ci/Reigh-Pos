<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Setting;
use App\Models\Table;
use App\Models\Category;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Tenants extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    // ─── Modal: Create ───────────────────────────────────────
    public $showCreateModal = false;
    public $name        = '';
    public $slug        = '';
    public $address     = '';
    public $phone       = '';
    public $logo        = null;

    // Owner Account Credentials (create only)
    public $ownerName     = '';
    public $ownerEmail    = '';
    public $ownerPassword = '';

    // ─── Modal: Edit ──────────────────────────────────────────
    public $showEditModal    = false;
    public $editTenantId     = null;
    public $editName         = '';
    public $editSlug         = '';
    public $editAddress      = '';
    public $editPhone        = '';
    public $editLogo         = null;   // new upload
    public $editLogoPreview  = null;   // existing logo path

    // Change owner (optional)
    public $editChangeOwner   = false;
    public $editOwnerName     = '';
    public $editOwnerEmail    = '';
    public $editOwnerPassword = '';

    // ─── Modal: Delete ────────────────────────────────────────
    public $showDeleteModal  = false;
    public $deleteTenantId   = null;
    public $deleteTenantName = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'slug', 'address', 'phone', 'logo', 'ownerName', 'ownerEmail', 'ownerPassword']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function createTenant()
    {
        $this->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:100|alpha_dash|unique:tenants,slug',
            'address'       => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'logo'          => 'nullable|image|max:1024',
            'ownerName'     => 'required|string|max:100',
            'ownerEmail'    => 'required|email|unique:users,email',
            'ownerPassword' => 'required|string|min:6',
        ]);

        $logoPath = null;
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
        }

        // 1. Buat Record Tenant
        $tenant = Tenant::create([
            'name'      => $this->name,
            'slug'      => $this->slug,
            'address'   => $this->address,
            'phone'     => $this->phone,
            'logo'      => $logoPath,
            'is_active' => true,
        ]);

        // 2. Buat Akun Owner / Manajer Restoran
        User::create([
            'name'      => $this->ownerName,
            'email'     => $this->ownerEmail,
            'password'  => bcrypt($this->ownerPassword),
            'role'      => UserRole::OWNER,
            'tenant_id' => $tenant->id,
        ]);

        // 3. Inisialisasi Pengaturan Awal Toko (Settings)
        $defaultSettings = [
            ['key' => 'store_name',         'value' => $tenant->name,    'description' => 'Nama Toko/Restoran'],
            ['key' => 'store_address',      'value' => $tenant->address, 'description' => 'Alamat Restoran'],
            ['key' => 'store_phone',        'value' => $tenant->phone,   'description' => 'Nomor Telepon Toko'],
            ['key' => 'store_logo',         'value' => $logoPath,        'description' => 'Logo Toko'],
            ['key' => 'tax_rate',           'value' => '11',             'description' => 'Pajak PB1 (%)'],
            ['key' => 'service_charge',     'value' => '5',              'description' => 'Service Charge (%)'],
            ['key' => 'printer_name',       'value' => 'Thermal_Printer','description' => 'Nama Printer Kasir'],
            ['key' => 'loyalty_enabled',    'value' => '1',              'description' => 'Status Fitur Poin Member'],
            ['key' => 'point_earn_rate',    'value' => '10000',          'description' => 'Belanja per Poin'],
            ['key' => 'point_redeem_value', 'value' => '100',            'description' => 'Nilai per 1 Poin (Rp)'],
        ];

        foreach ($defaultSettings as $s) {
            Setting::create([
                'key'         => $s['key'],
                'value'       => $s['value'],
                'description' => $s['description'],
                'tenant_id'   => $tenant->id,
            ]);
        }

        // 4. Inisialisasi Meja Awal (Meja 1 s/d Meja 5 + VIP)
        $tables = ['Meja 1', 'Meja 2', 'Meja 3', 'Meja 4', 'Meja 5', 'Meja VIP'];
        foreach ($tables as $tName) {
            Table::create([
                'tenant_id' => $tenant->id,
                'name'      => $tName,
                'slug'      => Str::slug($tName . '-' . $tenant->slug),
                'status'    => 'empty',
            ]);
        }

        // 5. Inisialisasi Kategori Awal
        $categories = [
            ['name' => 'Makanan Berat', 'slug' => 'makanan-berat-' . $tenant->slug],
            ['name' => 'Minuman',       'slug' => 'minuman-'       . $tenant->slug],
            ['name' => 'Cemilan',       'slug' => 'cemilan-'       . $tenant->slug],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'tenant_id' => $tenant->id,
                'name'      => $cat['name'],
                'slug'      => $cat['slug'],
            ]);
        }

        $this->closeCreateModal();
        session()->flash('message', "Restoran {$tenant->name} dan akun Owner ({$this->ownerEmail}) berhasil ditambahkan!");
    }

    // ──────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────
    public function openEditModal($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $this->resetValidation();
        $this->editTenantId      = $tenant->id;
        $this->editName          = $tenant->name;
        $this->editSlug          = $tenant->slug;
        $this->editAddress       = $tenant->address ?? '';
        $this->editPhone         = $tenant->phone ?? '';
        $this->editLogo          = null;
        $this->editLogoPreview   = $tenant->logo;
        $this->editChangeOwner   = false;
        $this->editOwnerName     = '';
        $this->editOwnerEmail    = '';
        $this->editOwnerPassword = '';

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editTenantId  = null;
    }

    public function updatedEditName($value)
    {
        $this->editSlug = Str::slug($value);
    }

    public function updateTenant()
    {
        $existingOwnerId = optional(
            User::where('tenant_id', $this->editTenantId)->where('role', UserRole::OWNER)->first()
        )->id;

        $rules = [
            'editName'    => 'required|string|max:100',
            'editSlug'    => 'required|string|max:100|alpha_dash|unique:tenants,slug,' . $this->editTenantId,
            'editAddress' => 'required|string|max:255',
            'editPhone'   => 'required|string|max:20',
            'editLogo'    => 'nullable|image|max:1024',
        ];

        if ($this->editChangeOwner) {
            $rules['editOwnerName']     = 'required|string|max:100';
            $rules['editOwnerEmail']    = 'required|email|unique:users,email,' . $existingOwnerId;
            $rules['editOwnerPassword'] = 'nullable|string|min:6';
        }

        $this->validate($rules, [], [
            'editName'          => 'Nama Restoran',
            'editSlug'          => 'Slug',
            'editAddress'       => 'Alamat',
            'editPhone'         => 'Telepon',
            'editLogo'          => 'Logo',
            'editOwnerName'     => 'Nama Owner',
            'editOwnerEmail'    => 'Email Owner',
            'editOwnerPassword' => 'Password Owner',
        ]);

        $tenant   = Tenant::findOrFail($this->editTenantId);
        $logoPath = $tenant->logo; // keep existing unless new file uploaded
        if ($this->editLogo) {
            $logoPath = $this->editLogo->store('logos', 'public');
        }

        $tenant->update([
            'name'    => $this->editName,
            'slug'    => $this->editSlug,
            'address' => $this->editAddress,
            'phone'   => $this->editPhone,
            'logo'    => $logoPath,
        ]);

        // Sync pengaturan store di settings table
        $settingsMap = [
            'store_name'    => $this->editName,
            'store_address' => $this->editAddress,
            'store_phone'   => $this->editPhone,
            'store_logo'    => $logoPath,
        ];
        foreach ($settingsMap as $key => $val) {
            Setting::where('tenant_id', $tenant->id)
                ->where('key', $key)
                ->update(['value' => $val]);
        }

        // Ganti / update Owner jika diminta
        if ($this->editChangeOwner) {
            $owner = User::where('tenant_id', $tenant->id)
                ->where('role', UserRole::OWNER)
                ->first();

            $ownerData = [
                'name'      => $this->editOwnerName,
                'email'     => $this->editOwnerEmail,
                'tenant_id' => $tenant->id,
                'role'      => UserRole::OWNER,
            ];
            if ($this->editOwnerPassword) {
                $ownerData['password'] = bcrypt($this->editOwnerPassword);
            }

            if ($owner) {
                $owner->update($ownerData);
            } else {
                // Tidak ada owner sebelumnya – buat baru (password wajib)
                if (!$this->editOwnerPassword) {
                    $this->addError('editOwnerPassword', 'Password wajib diisi untuk owner baru.');
                    return;
                }
                User::create($ownerData);
            }
        }

        $this->closeEditModal();
        session()->flash('message', "Data restoran {$tenant->name} berhasil diperbarui.");
    }

    // ──────────────────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────────────────
    public function openDeleteModal($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->deleteTenantId   = $tenant->id;
        $this->deleteTenantName = $tenant->name;
        $this->showDeleteModal  = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal  = false;
        $this->deleteTenantId   = null;
        $this->deleteTenantName = '';
    }

    public function deleteTenant()
    {
        $tenant = Tenant::findOrFail($this->deleteTenantId);
        $name   = $tenant->name;

        // Hapus semua data terkait (cascade manual)
        User::where('tenant_id', $tenant->id)->delete();
        Setting::where('tenant_id', $tenant->id)->delete();
        Table::where('tenant_id', $tenant->id)->delete();
        Category::where('tenant_id', $tenant->id)->delete();
        $tenant->delete();

        $this->closeDeleteModal();
        session()->flash('message', "Restoran \"{$name}\" beserta seluruh datanya telah dihapus.");
    }

    // ──────────────────────────────────────────────────────────
    // IMPERSONATE / TOGGLE STATUS
    // ──────────────────────────────────────────────────────────
    public function impersonate($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $owner  = User::where('tenant_id', $tenant->id)
            ->where('role', UserRole::OWNER)
            ->first();

        if (!$owner) {
            session()->flash('error', 'Tenant ini tidak memiliki Owner untuk di-impersonate.');
            return;
        }

        // Simpan ID Super Admin asli untuk nanti balik lagi
        session()->put('impersonator_id', auth()->user()->id);
        auth()->login($owner);

        return redirect()->route('dashboard');
    }

    public function toggleStatus($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update(['is_active' => !$tenant->is_active]);
        session()->flash('message', "Status {$tenant->name} berhasil diperbarui.");
    }

    // ──────────────────────────────────────────────────────────
    // RENDER
    // ──────────────────────────────────────────────────────────
    public function render()
    {
        $tenants = Tenant::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('slug', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.super-admin.tenants', [
            'tenants' => $tenants,
        ])->layout('components.admin-layout', ['header' => 'Manajemen Restoran']);
    }
}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Daftar Restoran & Cabang</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Kelola semua tenant dan tambah cabang restoran baru.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <div class="relative group w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search" class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white transition-all" placeholder="Cari nama / slug...">
            </div>

            <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 transition-all shrink-0 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Tambah Restoran</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
            <button @click="show = false"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
            <button @click="show = false"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    {{-- Tenants Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Restoran</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Info Kontak</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Terdaftar</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($tenants as $tenant)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 overflow-hidden flex-shrink-0 border border-slate-100 ring-4 ring-white shadow-sm">
                                        @if($tenant->logo)
                                            <img src="{{ asset('storage/' . $tenant->logo) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-indigo-500 font-black text-xl bg-gradient-to-br from-indigo-50 to-indigo-100">
                                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $tenant->name }}</h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-slate-400 font-medium">/{{ $tenant->slug }}</span>
                                            <a href="{{ url('/kiosk/' . $tenant->slug) }}" target="_blank" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded-md transition-colors" title="Buka Kiosk Restoran">Kiosk ↗</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-slate-700 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $tenant->phone ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-400 truncate max-w-xs font-medium">{{ $tenant->address ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($tenant->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 italic">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 ring-1 ring-rose-500/20 italic">Suspend</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-slate-600">
                                {{ $tenant->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Toggle Status --}}
                                    <button wire:click="toggleStatus({{ $tenant->id }})" 
                                            class="p-2.5 rounded-xl transition-all {{ $tenant->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}"
                                            title="{{ $tenant->is_active ? 'Suspend Restoran' : 'Aktifkan Restoran' }}">
                                        @if($tenant->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </button>

                                    {{-- Edit --}}
                                    <button wire:click="openEditModal({{ $tenant->id }})" 
                                            class="p-2.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-xl transition-all" 
                                            title="Edit Restoran">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    {{-- Impersonate --}}
                                    <button wire:click="impersonate({{ $tenant->id }})" 
                                            wire:loading.attr="disabled"
                                            class="p-2.5 bg-slate-50 text-slate-600 hover:bg-slate-900 hover:text-white rounded-xl transition-all disabled:opacity-50" 
                                            title="Masuk sebagai Owner Restoran Ini">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    </button>

                                    {{-- Hapus --}}
                                    <button wire:click="openDeleteModal({{ $tenant->id }})" 
                                            class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-all" 
                                            title="Hapus Restoran">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-bold italic">Tidak ada restoran ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 bg-slate-50/50">
            {{ $tenants->links() }}
        </div>
    </div>

    {{-- MODAL CREATE RESTORAN BARU --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div wire:click="closeCreateModal" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white w-full max-w-2xl rounded-3xl relative shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
            
            {{-- Modal Header --}}
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Tambah Cabang Restoran Baru</h3>
                        <p class="text-xs text-slate-400 font-medium">Buat cabang baru beserta akun manajer pengelolanya.</p>
                    </div>
                </div>
                <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Modal Form Content --}}
            <form wire:submit="createTenant" class="overflow-y-auto p-6 space-y-6 custom-scrollbar">
                
                {{-- Section 1: Detail Restoran --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2">1. Identitas Restoran</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nama Restoran <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live.debounce.300ms="name" placeholder="Contoh: Kopi Senja Cabang Barat" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                            @error('name') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Slug URL Restoran <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs text-slate-400 font-bold">/</span>
                                <input type="text" wire:model="slug" placeholder="kopi-senja-barat" class="w-full pl-6 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                            </div>
                            @error('slug') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                            @error('phone') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Logo Restoran (Opsional)</label>
                            <input type="file" wire:model="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @error('logo') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea wire:model="address" rows="2" placeholder="Jl. Kuliner No. 123, Jakarta" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800"></textarea>
                        @error('address') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Section 2: Akun Owner / Manajer --}}
                <div class="space-y-4 pt-2">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-2">2. Akun Owner / Manajer Restoran</h4>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Nama Pengelola / Owner <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="ownerName" placeholder="Nama Lengkap Manajer Cabang" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                        @error('ownerName') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Email Login <span class="text-rose-500">*</span></label>
                            <input type="email" wire:model="ownerEmail" placeholder="owner@cabang.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                            @error('ownerEmail') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Password Login <span class="text-rose-500">*</span></label>
                            <input type="password" wire:model="ownerPassword" placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-semibold text-slate-800">
                            @error('ownerPassword') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Starter Kit Notice --}}
                <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-indigo-900 leading-relaxed">
                        <strong>Auto Starter Kit:</strong> Restoran baru otomatis dilengkapi dengan 6 meja bawaan (Meja 1 s/d VIP), kategori standar, serta konfigurasi pengaturan awal siap pakai.
                    </p>
                </div>

                {{-- Modal Actions --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeCreateModal" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-all">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove>Simpan Restoran</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL EDIT RESTORAN --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div wire:click="closeEditModal" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="bg-white w-full max-w-2xl rounded-3xl relative shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">

            {{-- Modal Header --}}
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-amber-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Edit Restoran</h3>
                        <p class="text-xs text-slate-400 font-medium">Ubah nama, alamat, telepon, logo, atau owner.</p>
                    </div>
                </div>
                <button wire:click="closeEditModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit="updateTenant" class="overflow-y-auto p-6 space-y-6 custom-scrollbar">

                {{-- Section 1: Identitas --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-amber-600 uppercase tracking-widest border-b border-amber-100 pb-2">1. Identitas Restoran</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nama Restoran <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model.live.debounce.300ms="editName" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                            @error('editName') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Slug URL <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3 text-xs text-slate-400 font-bold">/</span>
                                <input type="text" wire:model="editSlug" class="w-full pl-6 pr-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                            </div>
                            @error('editSlug') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Nomor Telepon / WA <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="editPhone" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                            @error('editPhone') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Logo Baru (Opsional)</label>
                            @if($editLogoPreview)
                                <div class="mb-2 flex items-center gap-2">
                                    <img src="{{ asset('storage/' . $editLogoPreview) }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                    <span class="text-xs text-slate-400">Logo saat ini</span>
                                </div>
                            @endif
                            <input type="file" wire:model="editLogo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                            @error('editLogo') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea wire:model="editAddress" rows="2" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800"></textarea>
                        @error('editAddress') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Section 2: Ganti Owner (toggle) --}}
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h4 class="text-xs font-black text-amber-600 uppercase tracking-widest">2. Ganti Owner / Manajer</h4>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="editChangeOwner" class="w-4 h-4 rounded accent-amber-500">
                            <span class="text-xs font-bold text-slate-600">Ubah Owner</span>
                        </label>
                    </div>

                    @if($editChangeOwner)
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-700">Nama Owner Baru <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="editOwnerName" placeholder="Nama lengkap owner baru" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                        @error('editOwnerName') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Email Login <span class="text-rose-500">*</span></label>
                            <input type="email" wire:model="editOwnerEmail" placeholder="owner@email.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                            @error('editOwnerEmail') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">Password Baru <span class="text-slate-400">(Opsional)</span></label>
                            <input type="password" wire:model="editOwnerPassword" placeholder="Kosongkan jika tidak diubah" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm font-semibold text-slate-800">
                            @error('editOwnerPassword') <p class="text-[11px] text-rose-500 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @else
                    <p class="text-xs text-slate-400 italic">Centang checkbox di atas untuk mengubah data owner/manajer restoran ini.</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" wire:click="closeEditModal" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-lg shadow-amber-500/25 transition-all flex items-center gap-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="updateTenant">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateTenant">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL KONFIRMASI HAPUS --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div wire:click="closeDeleteModal" class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
        <div class="bg-white w-full max-w-md rounded-3xl relative shadow-2xl z-10 overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-1">Hapus Restoran?</h3>
                <p class="text-sm text-slate-500 mb-2">Anda akan menghapus restoran:</p>
                <p class="text-base font-black text-rose-600 mb-4">{{ $deleteTenantName }}</p>
                <div class="bg-rose-50 border border-rose-100 rounded-2xl p-3 mb-6 text-left">
                    <p class="text-xs text-rose-700 font-medium leading-relaxed">
                        ⚠️ <strong>Perhatian:</strong> Semua data terkait restoran ini akan ikut terhapus secara permanen, termasuk akun owner, pengaturan, meja, dan kategori produk. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="closeDeleteModal" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 transition-all">Batal</button>
                    <button wire:click="deleteTenant" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-lg shadow-rose-500/30 transition-all disabled:opacity-50">
                        <span wire:loading.remove wire:target="deleteTenant">Ya, Hapus Permanen</span>
                        <span wire:loading wire:target="deleteTenant">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

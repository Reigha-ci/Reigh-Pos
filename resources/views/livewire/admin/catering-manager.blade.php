<div class="space-y-6">

    <!-- Flash Message -->
    @if(session()->has('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
        </div>
    @endif

    <!-- Top Stats & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-white">Manajemen Pesanan Katering</h2>
            <p class="text-xs text-slate-400 mt-1">Kelola pesanan online, nasi box, dan jadwal pengiriman katering restoran.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('catering.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs flex items-center gap-2 border border-slate-700 transition-all">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Buka Web Katering</span>
            </a>

            <button wire:click="openManualModal" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-600 hover:to-rose-700 text-white font-black text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Pesanan Manual</span>
            </button>
        </div>
    </div>

    <!-- Metric Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div wire:click="setTab('pending')" class="p-4 rounded-2xl bg-[#0F1623] border border-slate-800/80 hover:border-amber-500/40 cursor-pointer transition-all">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block mb-1">Perlu Konfirmasi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-400">{{ $pendingCount }}</span>
                <span class="text-[10px] font-bold text-amber-500/80 bg-amber-500/10 px-2 py-0.5 rounded-full">Pending</span>
            </div>
        </div>

        <div wire:click="setTab('confirmed')" class="p-4 rounded-2xl bg-[#0F1623] border border-slate-800/80 hover:border-sky-500/40 cursor-pointer transition-all">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block mb-1">Dikonfirmasi</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-sky-400">{{ $confirmedCount }}</span>
                <span class="text-[10px] font-bold text-sky-500/80 bg-sky-500/10 px-2 py-0.5 rounded-full">Terjadwal</span>
            </div>
        </div>

        <div wire:click="setTab('cooking')" class="p-4 rounded-2xl bg-[#0F1623] border border-slate-800/80 hover:border-orange-500/40 cursor-pointer transition-all">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block mb-1">Sedang Dimasak</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-orange-400">{{ $cookingCount }}</span>
                <span class="text-[10px] font-bold text-orange-500/80 bg-orange-500/10 px-2 py-0.5 rounded-full">Di Dapur</span>
            </div>
        </div>

        <div wire:click="setTab('delivering')" class="p-4 rounded-2xl bg-[#0F1623] border border-slate-800/80 hover:border-indigo-500/40 cursor-pointer transition-all">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block mb-1">Dalam Pengiriman</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-black text-indigo-400">{{ $deliveringCount }}</span>
                <span class="text-[10px] font-bold text-indigo-500/80 bg-indigo-500/10 px-2 py-0.5 rounded-full">Diantar</span>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-[#0F1623] p-4 rounded-2xl border border-slate-800/80 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 no-scrollbar">
            @php
                $tabs = [
                    'all' => 'Semua',
                    'pending' => 'Menunggu Konfirmasi',
                    'confirmed' => 'Dikonfirmasi',
                    'cooking' => 'Sedang Dimasak',
                    'delivering' => 'Dalam Pengiriman',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            @foreach($tabs as $tabKey => $tabLabel)
                <button wire:click="setTab('{{ $tabKey }}')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $activeTab === $tabKey ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                    {{ $tabLabel }}
                </button>
            @endforeach
        </div>

        <!-- Date & Search -->
        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <!-- Quick Date Filters -->
            <select wire:model.live="dateFilter" class="bg-slate-900 text-slate-300 border-slate-800 rounded-xl text-xs font-bold py-2 px-3 focus:ring-amber-500">
                <option value="all_time">Semua Jadwal</option>
                <option value="today">Acara Hari Ini</option>
                <option value="tomorrow">Acara Besok</option>
                <option value="custom">Pilih Tanggal</option>
            </select>

            @if($dateFilter === 'custom')
                <input type="date" wire:model.live="customDate" class="bg-slate-900 text-slate-300 border-slate-800 rounded-xl text-xs py-2 px-2 focus:ring-amber-500">
            @endif

            <div class="relative w-full md:w-60">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pemesan / HP..." class="w-full pl-8 pr-3 py-2 bg-slate-900 text-slate-200 border-slate-800 rounded-xl text-xs focus:ring-amber-500">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

    </div>

    <!-- Orders Cards Grid / Table -->
    @if($orders->isEmpty())
        <div class="bg-[#0F1623] rounded-3xl p-12 text-center border border-slate-800/80">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-800/50 flex items-center justify-center text-slate-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <h3 class="text-base font-bold text-white mb-1">Belum Ada Pesanan Katering</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Tidak ada pesanan katering pada tab atau filter tanggal ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($orders as $order)
                @php
                    $status = $order->catering_status ?? $order->status;
                    $statusColor = match($status) {
                        'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'confirmed' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
                        'cooking' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
                        'delivering' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                        'completed' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                        default => 'bg-slate-800 text-slate-400 border-slate-700'
                    };
                @endphp
                <div class="bg-[#0F1623] rounded-3xl p-5 border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Card Header -->
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Pesanan Katering</span>
                                <h3 class="text-base font-black text-white group-hover:text-amber-400 transition-colors">#CAT-{{ $order->id }}</h3>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border uppercase tracking-wider {{ $statusColor }}">
                                {{ $status }}
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/60 mb-3 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-sm text-white">{{ $order->customer_name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $order->customer_phone }}</span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">
                                <strong class="text-slate-500">Alamat:</strong> {{ $order->delivery_address }}
                            </p>
                        </div>

                        <!-- Delivery Schedule Badge -->
                        <div class="flex items-center gap-2 text-xs mb-3">
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-300 font-bold border border-amber-500/20">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $order->delivery_date ? $order->delivery_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-semibold">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $order->delivery_time ?? '-' }} WIB</span>
                            </div>
                        </div>

                        <!-- Items Summary -->
                        <div class="text-xs text-slate-400 mb-3 divide-y divide-slate-800/60">
                            @foreach($order->items->take(2) as $item)
                                <div class="py-1 flex justify-between">
                                    <span class="truncate max-w-[180px]">{{ $item->quantity }}x {{ $item->product->name ?? 'Menu' }}</span>
                                    <span class="font-bold text-slate-300">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)
                                <div class="pt-1 text-[10px] text-slate-500 font-bold">
                                    + {{ $order->items->count() - 2 }} item menu lainnya
                                </div>
                            @endif
                        </div>

                        <!-- Rincian DP & Pelunasan Card -->
                        <div class="p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 text-xs space-y-1 mb-3">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Total Biaya:</span>
                                <span class="font-black text-amber-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-slate-400">DP ({{ $order->dp_percentage ?? 30 }}%):</span>
                                <span class="text-emerald-400 font-bold">Rp {{ number_format($order->dp_amount ?: ($order->total_price * 0.3), 0, ',', '.') }}</span>
                            </div>
                            @if($order->remaining_amount > 0)
                            <div class="flex justify-between items-center text-[11px] pt-1 border-t border-slate-800/60">
                                <span class="text-slate-400">Sisa:</span>
                                <span class="text-rose-400 font-bold">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }} <span class="text-[9px] text-slate-500 font-normal">({{ strtoupper($order->remaining_payment_method ?? 'COD') }})</span></span>
                            </div>
                            @endif
                            @if($order->payment_proof)
                                <div class="pt-1 text-[10px] text-emerald-400 font-bold flex items-center gap-1">
                                    <span>📷 Bukti Transfer Diunggah</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer & Actions -->
                    <div class="pt-3 border-t border-slate-800/80">
                        <div class="flex items-center justify-between mb-3 text-xs">
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono uppercase">
                                {{ $order->payment_method }} ({{ $order->status }})
                            </span>
                            <a href="{{ $order->customer_whatsapp_url }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 font-bold text-xs flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Chat Pelanggan</span>
                            </a>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                            <button wire:click="openDetail({{ $order->id }})" class="py-2 px-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-colors text-center">
                                Detail
                            </button>

                            @if(($order->catering_status ?? $order->status) === 'pending')
                                <button wire:click="approveOrder({{ $order->id }})" class="py-2 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition-colors shadow-xs">
                                    ✅ Setujui
                                </button>
                                <button wire:click="openRejectModal({{ $order->id }})" class="col-span-2 sm:col-span-1 py-2 px-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs transition-colors shadow-xs">
                                    ❌ Tolak
                                </button>
                            @elseif(($order->catering_status ?? $order->status) === 'confirmed')
                                <button wire:click="updateStatus({{ $order->id }}, 'cooking')" class="py-2 px-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-extrabold text-xs transition-colors shadow-xs">
                                    🍳 Masak
                                </button>
                            @elseif(($order->catering_status ?? $order->status) === 'cooking')
                                <button wire:click="updateStatus({{ $order->id }}, 'delivering')" class="py-2 px-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs transition-colors shadow-xs">
                                    🛵 Kirim
                                </button>
                            @elseif(($order->catering_status ?? $order->status) === 'delivering')
                                <button wire:click="updateStatus({{ $order->id }}, 'completed')" class="py-2 px-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition-colors shadow-xs">
                                    🏁 Selesai
                                </button>
                            @elseif(($order->catering_status ?? $order->status) === 'rejected')
                                <span class="py-2 px-2 rounded-xl bg-rose-500/10 text-rose-400 text-center text-xs font-bold border border-rose-500/20">
                                    Ditolak
                                </span>
                            @else
                                <span class="py-2 px-2 rounded-xl bg-slate-800/40 text-slate-500 text-center text-xs font-semibold">
                                    Tuntas
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- MODAL DETAIL PESANAN KATERING -->
    @if($isDetailModalOpen && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-[#0F1623] rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-800 relative text-white animate-slide-up">
                
                <div class="flex items-start justify-between pb-4 border-b border-slate-800 mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-black">Detail Pesanan #CAT-{{ $selectedOrder->id }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                {{ $selectedOrder->catering_status ?? $selectedOrder->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Dibuat: {{ $selectedOrder->created_at->format('d M Y H:i') }} WIB</p>
                    </div>
                    <button wire:click="closeDetail" class="p-1 text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-5 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    
                    <!-- Customer & Schedule Box -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-xs">
                        <div>
                            <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Pemesan</span>
                            <p class="font-extrabold text-sm text-white">{{ $selectedOrder->customer_name }}</p>
                            <p class="text-amber-400 font-mono">{{ $selectedOrder->customer_phone }}</p>
                            
                            <div class="mt-2 flex gap-2">
                                <a href="{{ $selectedOrder->customer_whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500 transition-colors">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    <span>Kirim Update WA</span>
                                </a>
                            </div>
                        </div>

                        <div>
                            <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Jadwal Pengantaran</span>
                            <p class="font-extrabold text-sm text-amber-400">
                                {{ $selectedOrder->delivery_date ? $selectedOrder->delivery_date->format('l, d M Y') : '-' }}
                            </p>
                            <p class="text-white font-semibold">Pukul: {{ $selectedOrder->delivery_time ?? '-' }} WIB</p>
                            
                            <div class="mt-2">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($selectedOrder->delivery_address) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-200 hover:text-white font-bold text-xs border border-slate-700 transition-colors">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Buka Google Maps</span>
                                </a>
                            </div>
                        </div>

                        <div class="sm:col-span-2 pt-3 border-t border-slate-800">
                            <span class="text-slate-500 font-bold uppercase text-[10px] block mb-1">Alamat Tujuan</span>
                            <p class="text-slate-200 leading-relaxed">{{ $selectedOrder->delivery_address }}</p>
                            @if($selectedOrder->note)
                                <p class="text-[11px] text-amber-400 bg-amber-500/10 p-2 rounded-xl mt-2 border border-amber-500/20">
                                    <strong>Catatan:</strong> {{ $selectedOrder->note }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Items List -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Menu Dipesan</h4>
                        <div class="divide-y divide-slate-800 text-xs">
                            @foreach($selectedOrder->items as $item)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="font-extrabold text-white text-sm block">{{ $item->product->name ?? 'Menu' }}</span>
                                        <span class="text-slate-400">{{ $item->quantity }} Porsi @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        @if($item->selectedVariants && $item->selectedVariants->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($item->selectedVariants as $v)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">
                                                        {{ $v->variant_name }}: {{ $v->option_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-extrabold text-sm text-white">
                                        Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Financial Summary & DP Details -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Menu</span>
                            <span class="text-white font-bold">Rp {{ number_format($selectedOrder->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedOrder->discount_amount > 0)
                            <div class="flex justify-between text-emerald-400">
                                <span>Potongan Diskon</span>
                                <span>-Rp {{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm font-black text-white pt-2 border-t border-slate-800">
                            <span>Total Biaya Pesanan</span>
                            <span class="text-amber-400 text-base">Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</span>
                        </div>

                        <!-- Info DP & Sisa -->
                        <div class="pt-2 border-t border-slate-800/80 space-y-1.5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400">Uang Muka / DP ({{ $selectedOrder->dp_percentage ?? 30 }}%):</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400 font-black">Rp {{ number_format($selectedOrder->dp_amount ?: ($selectedOrder->total_price * 0.3), 0, ',', '.') }}</span>
                                    @if($selectedOrder->catering_status === 'pending')
                                        <button wire:click="markDpPaid({{ $selectedOrder->id }})" class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold">
                                            Verifikasi DP
                                        </button>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">DP Masuk</span>
                                    @endif
                                </div>
                            </div>

                            @if($selectedOrder->remaining_amount > 0)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-400">Sisa Pelunasan:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-rose-400 font-black">Rp {{ number_format($selectedOrder->remaining_amount, 0, ',', '.') }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-bold uppercase">
                                        via {{ $selectedOrder->remaining_payment_method == 'cod' ? 'COD / Bayar di Tempat' : 'Transfer Bank / QRIS' }}
                                    </span>
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-between items-center pt-2 border-t border-slate-800/60 text-xs">
                                <span class="text-slate-400">Status Pembayaran Akhir:</span>
                                <div class="flex items-center gap-2">
                                    @if($selectedOrder->status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/30">LUNAS</span>
                                    @else
                                        <button wire:click="markAsPaid({{ $selectedOrder->id }})" class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-emerald-600 hover:from-amber-600 hover:to-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                                            Tandai Sisa Pembayaran Lunas
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Transfer Pelanggan Box -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 text-xs">
                        <span class="text-xs font-bold text-slate-300 block mb-2">Bukti Pembayaran / Transfer:</span>
                        @if($selectedOrder->payment_proof)
                            <div class="flex items-start gap-4">
                                <a href="{{ asset('storage/' . $selectedOrder->payment_proof) }}" target="_blank" class="block shrink-0 group relative rounded-xl overflow-hidden border border-slate-700">
                                    <img src="{{ asset('storage/' . $selectedOrder->payment_proof) }}" alt="Bukti Transfer" class="w-28 h-28 object-cover group-hover:scale-105 transition-transform">
                                    <span class="absolute inset-0 bg-black/40 flex items-center justify-center text-[10px] text-white opacity-0 group-hover:opacity-100 font-bold">Perbesar</span>
                                </a>
                                <div class="space-y-2">
                                    <p class="text-slate-300">Pelanggan telah mengunggah bukti transfer.</p>
                                    <a href="{{ asset('storage/' . $selectedOrder->payment_proof) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Buka Ukuran Asli</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <p class="text-slate-500 italic">Pelanggan belum mengunggah file bukti transfer melalui website.</p>
                        @endif
                    </div>

                    @if($selectedOrder->catering_status === 'rejected' && $selectedOrder->rejection_reason)
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-xs">
                            <span class="text-rose-400 font-bold block mb-1">Alasan Penolakan:</span>
                            <p class="text-rose-300">{{ $selectedOrder->rejection_reason }}</p>
                        </div>
                    @endif

                    <!-- Approval & Reject Actions -->
                    @if($selectedOrder->catering_status === 'pending')
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20">
                        <span class="text-xs font-bold text-amber-300 block mb-2">Tindakan Admin untuk Pesanan Baru:</span>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="approveOrder({{ $selectedOrder->id }})" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                <span>Setujui Pesanan</span>
                            </button>
                            <button wire:click="openRejectModal({{ $selectedOrder->id }})" class="py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs transition-all shadow-md shadow-rose-600/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Tolak Pesanan</span>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Status Change Workflow Buttons -->
                    <div class="pt-3 border-t border-slate-800">
                        <span class="text-xs font-bold text-slate-400 block mb-2">Ubah Status Produksi Katering:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                            <button wire:click="updateStatus({{ $selectedOrder->id }}, 'pending')" class="py-2 px-2 rounded-xl text-xs font-bold transition-all {{ $selectedOrder->catering_status === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                                Pending
                            </button>
                            <button wire:click="updateStatus({{ $selectedOrder->id }}, 'confirmed')" class="py-2 px-2 rounded-xl text-xs font-bold transition-all {{ $selectedOrder->catering_status === 'confirmed' ? 'bg-sky-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                                Dikonfirmasi
                            </button>
                            <button wire:click="updateStatus({{ $selectedOrder->id }}, 'cooking')" class="py-2 px-2 rounded-xl text-xs font-bold transition-all {{ $selectedOrder->catering_status === 'cooking' ? 'bg-orange-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                                Dimasak
                            </button>
                            <button wire:click="updateStatus({{ $selectedOrder->id }}, 'delivering')" class="py-2 px-2 rounded-xl text-xs font-bold transition-all {{ $selectedOrder->catering_status === 'delivering' ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                                Pengantaran
                            </button>
                            <button wire:click="updateStatus({{ $selectedOrder->id }}, 'completed')" class="py-2 px-2 rounded-xl text-xs font-bold transition-all {{ $selectedOrder->catering_status === 'completed' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                                Selesai
                            </button>
                        </div>
                    </div>

                </div>

                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('catering.invoice', $selectedOrder->id) }}" target="_blank" class="text-xs font-bold text-amber-400 hover:underline inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span>Lihat Invoice</span>
                        </a>
                        <a href="{{ $selectedOrder->customer_whatsapp_url }}" target="_blank" class="text-xs font-bold text-emerald-400 hover:underline inline-flex items-center gap-1">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            <span>WhatsApp Pelanggan</span>
                        </a>
                    </div>

                    <button wire:click="closeDetail" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL TOLAK PESANAN KATERING -->
    @if($isRejectModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-[#0F1623] rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-800 text-white animate-slide-up">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                <h3 class="text-lg font-black text-rose-400">Tolak Pesanan Katering #CAT-{{ $rejectOrderId }}</h3>
                <button wire:click="closeRejectModal" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <p class="text-xs text-slate-400 mb-4">Masukkan alasan penolakan. Alasan ini akan tampil di invoice pelanggan dan tersimpan di sistem.</p>
            <form wire:submit.prevent="rejectOrder" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea wire:model="rejectionReason" rows="3" placeholder="Contoh: Jadwal slot masak di tanggal tersebut sudah penuh / Pembayaran DP belum masuk." class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-3 focus:ring-rose-500 focus:border-rose-500"></textarea>
                    @error('rejectionReason') <span class="text-rose-400 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" wire:click="closeRejectModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black">Tolak Pesanan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL INPUT PESANAN KATERING MANUAL (ADMIN/KASIR) -->
    @if($isManualModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-[#0F1623] rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-800 relative text-white animate-slide-up">
                
                <div class="flex items-start justify-between pb-3 border-b border-slate-800 mb-5">
                    <div>
                        <h3 class="text-xl font-black">Input Pesanan Katering Manual</h3>
                        <p class="text-xs text-slate-400">Untuk pesanan via telepon atau pelanggan offline.</p>
                    </div>
                    <button wire:click="closeManualModal" class="p-1 text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitManualOrder" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Nama Pemesan *</label>
                            <input type="text" wire:model="manualName" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500">
                            @error('manualName') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">No. WhatsApp / HP *</label>
                            <input type="text" wire:model="manualPhone" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500">
                            @error('manualPhone') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Tanggal Acara / Pengiriman *</label>
                            <input type="date" wire:model="manualDate" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500">
                            @error('manualDate') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Jam Pengantaran *</label>
                            <input type="time" wire:model="manualTime" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500">
                            @error('manualTime') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Alamat Lengkap Pengiriman *</label>
                        <textarea wire:model="manualAddress" rows="2" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500"></textarea>
                        @error('manualAddress') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Catatan Pesanan Khusus</label>
                        <input type="text" wire:model="manualNote" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2.5 focus:ring-amber-500">
                    </div>

                    <!-- Items Selection -->
                    <div class="pt-3 border-t border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-300">Item Menu Katering *</label>
                            <button type="button" wire:click="addItemRow" class="text-xs text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1">
                                + Tambah Menu
                            </button>
                        </div>

                        <div class="space-y-2">
                            @foreach($manualItems as $index => $item)
                                <div class="flex items-center gap-2">
                                    <select wire:change="onProductSelected({{ $index }}, $event.target.value)" class="flex-1 text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2 focus:ring-amber-500">
                                        <option value="">-- Pilih Menu --</option>
                                        @foreach($availableProducts as $prod)
                                            <option value="{{ $prod->id }}" {{ ($item['product_id'] ?? '') == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} (Rp {{ number_format($prod->price, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="w-24">
                                        <input type="number" min="1" wire:model.live="manualItems.{{ $index }}.quantity" placeholder="Porsi" class="w-full text-xs rounded-xl bg-slate-900 border-slate-700 text-white p-2 text-center focus:ring-amber-500">
                                    </div>

                                    <button type="button" wire:click="removeItemRow({{ $index }})" class="p-2 text-rose-400 hover:text-rose-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Summary & Submit -->
                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Biaya:</span>
                            <span class="text-base font-black text-amber-400">Rp {{ number_format($this->getManualTotal(), 0, ',', '.') }}</span>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" wire:click="closeManualModal" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-600 hover:to-rose-700 text-white text-xs font-black shadow-lg shadow-amber-500/20">
                                Simpan Pesanan Katering
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>

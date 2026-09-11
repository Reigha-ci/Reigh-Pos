<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
    
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-10 mb-10 shadow-2xl border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-60 h-60 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-500/30">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Layanan Katering & Nasi Box Online
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight mb-4">
                Pesan Katering Praktis & Lezat untuk Segala Acara
            </h1>
            <p class="text-sm sm:text-base text-slate-300 font-normal leading-relaxed mb-6">
                Nikmati hidangan spesial dari <strong class="text-white">{{ $storeName }}</strong> untuk syukuran, rapat kantor, gathering, arisan, hingga pesta keluarga. Pengantaran tepat waktu dengan kualitas rasa terbaik.
            </p>

            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-200">
                <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Pesan Minimal H-1</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>100% Halal & Higienis</span>
                </div>
                <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    <span>Kirim Sampai ke Lokasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Menu Catalog & Floating Cart Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" id="menu-section">
        
        <!-- Left Column: Filter & Menu List (Col 1-8) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Search & Filter Bar -->
            <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Cari menu katering..." class="w-full pl-9 pr-3.5 py-2 rounded-xl text-sm border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 no-scrollbar">
                    <button wire:click="setCategory('all')" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $activeCategory === 'all' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Menu
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="setCategory({{ $cat->id }})" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $activeCategory == $cat->id ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid -->
            @if($products->isEmpty())
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1">Menu Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($products as $product)
                        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 hover:border-amber-300 hover:shadow-lg transition-all duration-200 flex flex-col justify-between group">
                            <div>
                                <!-- Image or Fallback -->
                                <div class="relative w-full h-44 rounded-xl overflow-hidden mb-3 bg-slate-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-amber-50 to-orange-100 text-amber-600">
                                            <svg class="w-10 h-10 opacity-70 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span class="text-[10px] font-bold uppercase tracking-wider">{{ \App\Models\Setting::value('store_name', 'Menu Katering') }}</span>
                                        </div>
                                    @endif

                                    @if($product->variants && $product->variants->isNotEmpty())
                                        <span class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-extrabold uppercase tracking-wide">
                                            Ada Pilihan
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-start justify-between gap-2 mb-1.5">
                                    <h3 class="font-bold text-slate-900 text-base group-hover:text-amber-600 transition-colors leading-snug">
                                        {{ $product->name }}
                                    </h3>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-2 mb-3 leading-relaxed">
                                    {{ $product->description ?? 'Paket menu katering istimewa dimasak dengan rempah berkualitas.' }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold uppercase block">Harga / Porsi</span>
                                        <span class="text-base font-extrabold text-amber-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Quick Action Buttons for Catering -->
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button wire:click="addToCart({{ $product->id }}, 1)" class="py-2 px-1 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-extrabold text-xs transition-colors flex items-center justify-center gap-1">
                                        <span>+1</span>
                                    </button>
                                    <button wire:click="addToCart({{ $product->id }}, 10)" class="py-2 px-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-xs transition-colors flex items-center justify-center gap-1">
                                        <span>+10 Pax</span>
                                    </button>
                                    <button wire:click="addToCart({{ $product->id }}, 25)" class="py-2 px-1 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs transition-colors flex items-center justify-center gap-1">
                                        <span>+25 Pax</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Right Column: Cart & Summary (Col 9-12) -->
        <div class="lg:col-span-4 sticky top-24 space-y-4">
            
            <div class="bg-white rounded-3xl p-5 shadow-xl border border-slate-200/80">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="font-black text-slate-900 text-base">Keranjang Katering</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-xs">
                        {{ $this->getTotalItems() }} Porsi
                    </span>
                </div>

                @if(empty($cart))
                    <div class="py-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-600 mb-1">Keranjang masih kosong</p>
                        <p class="text-[11px] text-slate-400">Pilih menu di samping untuk menentukan porsi katering Anda.</p>
                    </div>
                @else
                    <!-- Cart Items List -->
                    <div class="space-y-3 max-h-72 overflow-y-auto pr-1 custom-scrollbar mb-4">
                        @foreach($cart as $uuid => $item)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-800">{{ $item['name'] }}</h4>
                                        <span class="text-[11px] text-amber-600 font-semibold">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                        @if(!empty($item['variants']))
                                            <div class="mt-1 space-y-0.5">
                                                @foreach($item['variants'] as $v)
                                                    <span class="inline-block text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/50">
                                                        {{ $v['variant_name'] }}: {{ $v['option_name'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <button wire:click="removeFromCart('{{ $uuid }}')" class="text-slate-400 hover:text-rose-500 p-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-200/50">
                                    <div class="flex items-center gap-1.5">
                                        <button wire:click="decrementQty('{{ $uuid }}', 5)" class="w-6 h-6 rounded-md bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 flex items-center justify-center" title="Kurangi 5">-5</button>
                                        <button wire:click="decrementQty('{{ $uuid }}', 1)" class="w-6 h-6 rounded-md bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 flex items-center justify-center">-</button>
                                        <input type="number" min="1" value="{{ $item['qty'] }}" wire:change="setItemQty('{{ $uuid }}', $event.target.value)" class="w-14 text-center py-0.5 px-1 rounded-md text-xs font-bold border-slate-200 bg-white">
                                        <button wire:click="incrementQty('{{ $uuid }}', 1)" class="w-6 h-6 rounded-md bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 flex items-center justify-center">+</button>
                                        <button wire:click="incrementQty('{{ $uuid }}', 5)" class="w-6 h-6 rounded-md bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 flex items-center justify-center" title="Tambah 5">+5</button>
                                    </div>
                                    <span class="font-extrabold text-xs text-slate-900">
                                        Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Calculations -->
                    <div class="space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Subtotal Menu</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                        </div>
                        @if($this->getDiscountAmount() > 0)
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Promo ({{ $appliedPromoName }})</span>
                                <span>-Rp {{ number_format($this->getDiscountAmount(), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->getPointDiscount() > 0)
                            <div class="flex justify-between text-indigo-600 font-semibold">
                                <span>Tukar Poin Member</span>
                                <span>-Rp {{ number_format($this->getPointDiscount(), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-slate-200 font-black text-sm text-slate-900">
                            <span>Estimasi Total</span>
                            <span class="text-amber-600 text-base">Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-5">
                        <button wire:click="openCheckout" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-amber-500 to-rose-500 text-white font-black text-sm shadow-lg shadow-amber-500/25 hover:from-amber-600 hover:to-rose-600 transition-all flex items-center justify-center gap-2">
                            <span>Isi Data Pengiriman</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Info Guide Box -->
            <div class="bg-amber-50/70 rounded-2xl p-4 border border-amber-200/60 text-xs text-amber-900 space-y-2">
                <div class="flex items-center gap-2 font-bold text-amber-800">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Butuh Konsultasi Menu Katering?</span>
                </div>
                <p class="text-amber-800/80 leading-relaxed text-[11px]">
                    Ingin paket custom prasmanan, ganti menu lauk, atau pemesanan di atas 100 porsi? Langsung hubungi WhatsApp admin kami untuk penawaran khusus.
                </p>
            </div>

        </div>

    </div>

    <!-- MODAL VARIANT SELECTION -->
    @if($isVariantModalOpen && $selectedProduct)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative animate-slide-up">
                
                <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="font-black text-slate-900 text-lg">{{ $selectedProduct->name }}</h3>
                        <p class="text-xs text-amber-600 font-bold">Harga Dasar: Rp {{ number_format($selectedProduct->price, 0, ',', '.') }}</p>
                    </div>
                    <button wire:click="closeVariantModal" class="p-1 text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if($errors->has('variant_error'))
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-600 text-xs font-semibold mb-4 border border-rose-200">
                        {{ $errors->first('variant_error') }}
                    </div>
                @endif

                <div class="space-y-4 max-h-80 overflow-y-auto pr-1 custom-scrollbar mb-4">
                    @foreach($selectedProduct->variants as $variant)
                        <div>
                            <label class="font-bold text-xs text-slate-800 block mb-2">
                                {{ $variant->name }} @if($variant->is_required) <span class="text-rose-500">*</span> @endif
                            </label>

                            <div class="space-y-1.5">
                                @foreach($variant->options as $option)
                                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:border-amber-400 cursor-pointer text-xs font-medium bg-slate-50/50 hover:bg-amber-50/30 transition-colors">
                                        <div class="flex items-center gap-2">
                                            @if($variant->type == 'radio')
                                                <input type="radio" wire:model.live="selectedVariants.{{ $variant->id }}" value="{{ $option->id }}" class="text-amber-600 focus:ring-amber-500">
                                            @else
                                                <input type="checkbox" wire:model.live="selectedVariants.{{ $variant->id }}.{{ $option->id }}" class="rounded text-amber-600 focus:ring-amber-500">
                                            @endif
                                            <span>{{ $option->name }}</span>
                                        </div>
                                        @if($option->price > 0)
                                            <span class="text-[11px] font-bold text-amber-700">+Rp {{ number_format($option->price, 0, ',', '.') }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Jumlah Porsi Input -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="font-bold text-xs text-slate-800 block mb-1.5">Jumlah Porsi (Pax):</label>
                        <div class="flex items-center gap-2">
                            <input type="number" min="1" wire:model="variantQty" class="w-full rounded-xl border-slate-200 text-sm font-bold p-2.5">
                            <div class="flex gap-1">
                                <button type="button" wire:click="$set('variantQty', 10)" class="px-2.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold">10</button>
                                <button type="button" wire:click="$set('variantQty', 25)" class="px-2.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold">25</button>
                                <button type="button" wire:click="$set('variantQty', 50)" class="px-2.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold">50</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total / Porsi:</span>
                        <span class="text-base font-extrabold text-amber-600">Rp {{ number_format($currentPrice, 0, ',', '.') }}</span>
                    </div>
                    <button wire:click="saveVariantToCart" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shadow-amber-600/20 transition-all">
                        Simpan ke Keranjang
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- MODAL CHECKOUT & FORM PENGIRIMAN KATERING -->
    @if($isCheckoutOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative animate-slide-up">
                
                <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-5">
                    <div>
                        <h3 class="font-black text-slate-900 text-xl">Form Pemesanan Katering</h3>
                        <p class="text-xs text-slate-500">Lengkapi data acara dan pengantaran untuk konfirmasi pesanan.</p>
                    </div>
                    <button wire:click="closeCheckout" class="p-1 text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if($errors->has('submit_error'))
                    <div class="p-3 rounded-xl bg-rose-50 text-rose-600 text-xs font-semibold mb-4 border border-rose-200">
                        {{ $errors->first('submit_error') }}
                    </div>
                @endif

                <form wire:submit.prevent="submitOrder" class="space-y-4 max-h-[75vh] overflow-y-auto pr-2 custom-scrollbar">
                    
                    <!-- Nama & WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pemesan / Penanggung Jawab <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="customerName" placeholder="Contoh: Bpk. Robby / Ibu Sarah" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500 @error('customerName') border-rose-400 @enderror">
                            @error('customerName') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <input type="text" wire:model="customerPhone" placeholder="081234567890" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500 @error('customerPhone') border-rose-400 @enderror">
                            <span class="text-[10px] text-slate-400 mt-1 block">Untuk konfirmasi & info pengantaran</span>
                            @error('customerPhone') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Tanggal Acara & Jam Pengantaran -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Acara / Pengiriman <span class="text-rose-500">*</span></label>
                            <input type="date" wire:model="deliveryDate" min="{{ date('Y-m-d') }}" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500 @error('deliveryDate') border-rose-400 @enderror">
                            @error('deliveryDate') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu / Jam Sampai di Lokasi <span class="text-rose-500">*</span></label>
                            <input type="time" wire:model="deliveryTime" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500 @error('deliveryTime') border-rose-400 @enderror">
                            @error('deliveryTime') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Alamat Lengkap Pengiriman -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Lengkap Pengantaran Katering <span class="text-rose-500">*</span></label>
                        <textarea wire:model="deliveryAddress" rows="2" placeholder="Nama Jalan, No. Rumah/Kantor, RT/RW, Kelurahan, Patokan atau Ruangan Acara..." class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500 @error('deliveryAddress') border-rose-400 @enderror"></textarea>
                        @error('deliveryAddress') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Catatan Khusus Acara -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="orderNote" placeholder="Contoh: Sambal dipisah, minta sendok garpu di setiap box, dll." class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Loyalty Member Poin (Jika ada) -->
                    @if($loyaltyEnabled && $isMember && $memberPoints > 0)
                        <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200/70 text-xs">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-indigo-900">Member Terdeteksi ({{ $memberPoints }} Poin)</span>
                                <span class="text-[11px] text-indigo-700">Nilai: Rp {{ number_format($memberPoints * $pointRedeemValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" wire:model.live="pointsToRedeem" min="0" max="{{ $memberPoints }}" placeholder="Jumlah poin yg ingin ditukar" class="w-full text-xs rounded-xl border-indigo-200 p-2 bg-white">
                                <button type="button" wire:click="$set('pointsToRedeem', {{ $memberPoints }})" class="px-3 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs shrink-0">
                                    Tukar Semua
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Pembayaran (DP & Lunas) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilihan Pembayaran Saat Pesan (DP / Lunas) <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2.5 mb-3">
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all {{ $dpPercentage == 30 ? 'border-amber-500 bg-amber-50/60 text-amber-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700' }}">
                                <input type="radio" wire:model.live="dpPercentage" value="30" class="hidden">
                                <span class="block text-xs font-black">DP 30%</span>
                                <span class="text-[9px] text-slate-400 block font-normal">Minimal DP</span>
                            </label>
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all {{ $dpPercentage == 50 ? 'border-amber-500 bg-amber-50/60 text-amber-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700' }}">
                                <input type="radio" wire:model.live="dpPercentage" value="50" class="hidden">
                                <span class="block text-xs font-black">DP 50%</span>
                                <span class="text-[9px] text-slate-400 block font-normal">Uang Muka 50%</span>
                            </label>
                            <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all {{ $dpPercentage == 100 ? 'border-amber-500 bg-amber-50/60 text-amber-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700' }}">
                                <input type="radio" wire:model.live="dpPercentage" value="100" class="hidden">
                                <span class="block text-xs font-black">Lunas 100%</span>
                                <span class="text-[9px] text-slate-400 block font-normal">Bayar Penuh</span>
                            </label>
                        </div>
                        <input type="hidden" wire:model="paymentMethod" value="transfer">
                        <p class="text-[10px] text-slate-500 mb-4">Pembayaran DP dilakukan via <strong>Transfer Bank / QRIS</strong> ke rekening admin resto setelah mengirim pesanan.</p>

                        @if($dpPercentage < 100)
                        <label class="block text-xs font-bold text-slate-700 mb-2">Metode Pelunasan Sisa Pembayaran <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label class="p-3 rounded-2xl border cursor-pointer transition-all flex items-center gap-3 {{ $remainingPaymentMethod === 'transfer' ? 'border-amber-500 bg-amber-50/40 text-amber-950 font-bold' : 'border-slate-200 bg-white text-slate-700' }}">
                                <input type="radio" wire:model.live="remainingPaymentMethod" value="transfer" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-xs font-bold">Transfer Bank / QRIS</span>
                                    <span class="text-[10px] text-slate-500 font-normal">Dilunasi sebelum hari H pengiriman</span>
                                </div>
                            </label>
                            <label class="p-3 rounded-2xl border cursor-pointer transition-all flex items-center gap-3 {{ $remainingPaymentMethod === 'cod' ? 'border-amber-500 bg-amber-50/40 text-amber-950 font-bold' : 'border-slate-200 bg-white text-slate-700' }}">
                                <input type="radio" wire:model.live="remainingPaymentMethod" value="cod" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-xs font-bold">Bayar di Tempat (COD)</span>
                                    <span class="text-[10px] text-slate-500 font-normal">Dibayarkan saat katering diantar</span>
                                </div>
                            </label>
                        </div>
                        @endif
                    </div>

                    <!-- Ringkasan Pembayaran -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Total Porsi Katering:</span>
                            <span class="font-bold text-slate-900">{{ $this->getTotalItems() }} Pax</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Menu:</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                        </div>
                        @if($this->getDiscountAmount() > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon Promo:</span>
                                <span class="font-bold">-Rp {{ number_format($this->getDiscountAmount(), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->getPointDiscount() > 0)
                            <div class="flex justify-between text-indigo-600">
                                <span>Diskon Poin:</span>
                                <span class="font-bold">-Rp {{ number_format($this->getPointDiscount(), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                            <span>Total Harga:</span>
                            <span>Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-base font-black text-slate-900 pt-2 mt-2 border-t border-slate-200">
                            <span>Harus Dibayar Sekarang (DP {{ $dpPercentage }}%):</span>
                            <span class="text-amber-600">Rp {{ number_format($this->getGrandTotal() * ($dpPercentage / 100), 0, ',', '.') }}</span>
                        </div>
                        @if($dpPercentage < 100)
                        <div class="flex justify-between text-sm font-bold text-slate-600 pt-1">
                            <span>Sisa Pelunasan:</span>
                            <span class="text-rose-600">Rp {{ number_format($this->getGrandTotal() - ($this->getGrandTotal() * ($dpPercentage / 100)), 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-amber-500 to-rose-500 text-white font-black text-sm shadow-xl shadow-amber-500/25 hover:from-amber-600 hover:to-rose-600 transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove>Kirim Pesanan Katering</span>
                            <span wire:loading class="inline-flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Memproses Pesanan...
                            </span>
                        </button>
                        <p class="text-center text-[10px] text-slate-400 mt-2">
                            Setelah klik kirim, Anda akan diarahkan ke invoice dan dapat langsung konfirmasi otomatis ke WhatsApp {{ $storeName }}.
                        </p>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>

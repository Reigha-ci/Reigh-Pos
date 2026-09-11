<div class="relative min-h-screen pb-32" x-data>
    {{-- Midtrans Snap JS --}}
    @php
        $midtransJsUrl = \App\Models\Setting::value('midtrans_is_production') == '1' 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = \App\Models\Setting::value('midtrans_client_key');
    @endphp
    <script src="{{ $midtransJsUrl }}" data-client-key="{{ $clientKey }}"></script>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('pay-with-midtrans', (event) => {
                window.snap.pay(event.token, {
                    onSuccess: function(result) {
                        window.location.reload();
                    },
                    onPending: function(result) {
                        window.location.reload();
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            });
        });
    </script>

    {{-- NOTIFIKASI PELAYAN --}}
    @if(session()->has('success_waitress'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition
        class="fixed top-24 left-1/2 -translate-x-1/2 z-[150] w-max max-w-[90%] px-4">
        <div class="flex items-center gap-3 px-4 py-3 bg-slate-900/95 backdrop-blur-md text-white rounded-full shadow-2xl border border-slate-700">
            <span class="text-xs font-bold">{{ session('success_waitress') }}</span>
        </div>
    </div>
    @endif

    @php
        $tenant = $table->tenant ?? null;
        $storeName = $tenant->name ?? \App\Models\Setting::value('store_name', 'KedaiRobby.id');
        $storeLogo = $tenant->logo ?? \App\Models\Setting::value('store_logo');
    @endphp

    {{-- HEADER --}}
    <div class="sticky top-0 z-40 bg-[#F6F8FC]/95 backdrop-blur-md border-b border-slate-200/60 shadow-sm transition-all">
        <div class="px-5 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if($storeLogo)
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" class="w-10 h-10 rounded-xl object-cover shadow-md shadow-indigo-500/20 border border-slate-200/60 bg-white">
                @else
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 font-black text-sm">
                        {{ strtoupper(substr($storeName, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-base font-black text-slate-800 leading-none tracking-tight">{{ $storeName }}</h1>
                    <p class="text-[0.65rem] text-slate-500 font-bold uppercase tracking-widest mt-1">{{ $table_name }}</p>
                </div>
            </div>
            <button wire:click="callWaitress" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-500 border border-slate-200 shadow-sm hover:text-red-500">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 12.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg>
            </button>
        </div>

        {{-- KATEGORI --}}
        <div class="flex overflow-x-auto gap-2 px-5 pb-3 pt-1 no-scrollbar scroll-smooth">
            <button wire:click="setCategory('all')" 
                class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border {{ $activeCategory === 'all' ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-500 border-slate-200' }}">
                Semua
            </button>
            @foreach($categories as $category)
                <button wire:click="setCategory({{ $category->id }})" 
                    class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition-all border {{ $activeCategory == $category->id ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-500 border-slate-200' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- LIST PRODUK --}}
    <div class="p-5 space-y-4 min-h-[60vh]">
        @foreach($products as $product)
            @php
                $isSoldOut = $product->stock <= 0 || !$product->is_available;
                
                // Cek Qty di Cart (Global)
                $qtyInCart = 0;
                foreach($cart as $item) {
                    if($item['product_id'] == $product->id) $qtyInCart += $item['qty'];
                }
            @endphp

            <div wire:key="product-{{ $product->id }}" class="bg-white p-3 rounded-[1.25rem] shadow-sm border border-slate-100 flex gap-4 {{ $isSoldOut ? 'opacity-60 grayscale' : '' }}">
                <div class="w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden relative bg-slate-100">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif

                    @if($isSoldOut)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center"><span class="text-white text-[10px] font-black uppercase tracking-widest border border-white px-2 py-1 rounded">Habis</span></div>
                    @elseif($qtyInCart > 0)
                        <div class="absolute top-1 left-1 bg-indigo-600 text-white text-[10px] font-bold w-6 h-6 flex items-center justify-center rounded-lg shadow-md border-2 border-white">{{ $qtyInCart }}</div>
                    @endif
                </div>

                <div class="flex-1 flex flex-col justify-between py-1">
                    <div>
                        <h3 class="font-bold text-slate-800 text-[0.95rem] leading-tight">{{ $product->name }}</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 mt-1">{{ $product->description }}</p>
                    </div>

                    <div class="flex items-end justify-between mt-2">
                        <div>
                            <span class="block font-black text-slate-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @if($product->variants->isNotEmpty())
                                <span class="text-[9px] text-indigo-500 font-bold bg-indigo-50 px-1.5 py-0.5 rounded">Opsi Tersedia</span>
                            @endif
                        </div>

                        @if(!$isSoldOut)
                            <button wire:click="addToCart({{ $product->id }})" class="bg-indigo-50 text-indigo-600 border border-indigo-100 px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                + Tambah
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL VARIAN --}}
    @if($isVariantModalOpen && $selectedProduct)
    <div class="fixed inset-0 z-[120] flex items-end justify-center px-4 pb-4">
        <div wire:click="$set('isVariantModalOpen', false)" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white w-full max-w-md rounded-[2rem] relative shadow-2xl animate-slide-up max-h-[85vh] flex flex-col overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-black text-lg text-slate-800">{{ $selectedProduct->name }}</h3>
                    <p class="text-xs text-slate-500">Sesuaikan pesananmu</p>
                </div>
                <button wire:click="$set('isVariantModalOpen', false)" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>

            <div class="p-5 overflow-y-auto custom-scrollbar space-y-6">
                @foreach($selectedProduct->variants as $variant)
                <div>
                    <div class="flex justify-between mb-2">
                        <h4 class="font-bold text-slate-700 text-sm">{{ $variant->name }}</h4>
                        @if($variant->is_required) <span class="text-[10px] text-red-500 bg-red-50 px-2 rounded-full font-bold">Wajib</span> @endif
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($variant->options as $option)
                        <label class="flex items-center justify-between p-3 rounded-xl border {{ 
                            ($variant->type == 'radio' && isset($selectedVariants[$variant->id]) && $selectedVariants[$variant->id] == $option->id) ||
                            ($variant->type == 'checkbox' && isset($selectedVariants[$variant->id][$option->id]) && $selectedVariants[$variant->id][$option->id]) 
                            ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200' 
                        }} cursor-pointer transition-all">
                            <div class="flex items-center gap-3">
                                @if($variant->type == 'radio')
                                    <input type="radio" wire:model.live="selectedVariants.{{ $variant->id }}" value="{{ $option->id }}" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                @else
                                    <input type="checkbox" wire:model.live="selectedVariants.{{ $variant->id }}.{{ $option->id }}" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                @endif
                                <span class="text-sm font-medium text-slate-700">{{ $option->name }}</span>
                            </div>
                            @if($option->price > 0)
                                <span class="text-xs font-bold text-slate-500">+Rp {{ number_format($option->price, 0, ',', '.') }}</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
                
                @error('variant_error') <div class="bg-red-100 text-red-600 text-xs font-bold p-3 rounded-lg text-center">{{ $message }}</div> @enderror
            </div>

            <div class="p-5 border-t border-slate-100 bg-white">
                <button wire:click="saveVariantToCart" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg flex justify-between px-6">
                    <span>Masuk Keranjang</span>
                    <span>Rp {{ number_format($currentPrice, 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- TOMBOL FLOATING CHECKOUT --}}
    @if(!empty($cart))
    <div class="fixed bottom-0 left-0 w-full px-5 pb-6 z-[100] pointer-events-none flex justify-center">
        <div class="w-full max-w-md pointer-events-auto">
            <button wire:click="openCheckout" class="w-full bg-slate-900 text-white p-3.5 rounded-2xl shadow-2xl flex justify-between items-center group active:scale-[0.98] transition-all cursor-pointer">
                <div class="flex items-center gap-3 pl-1">
                    <div class="bg-indigo-500 text-white w-10 h-10 flex items-center justify-center rounded-full font-black text-sm border-2 border-slate-800 animate-bounce">
                        {{ $this->getTotalItems() }}
                    </div>
                    <div class="text-left flex flex-col">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Bayar</span>
                        <span class="font-black text-lg leading-none">Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="bg-white text-slate-900 px-5 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2">
                    Lanjut Bayar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </button>
        </div>
    </div>
    @endif

    {{-- MODAL CHECKOUT --}}
    @if($isCheckoutOpen)
    <div class="fixed inset-0 z-[110] flex items-end justify-center px-2 pb-2">
        <div wire:click="closeCheckout" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white w-full max-w-md rounded-t-[2rem] md:rounded-[2rem] relative shadow-2xl animate-slide-up max-h-[90vh] flex flex-col overflow-hidden">
            
            <div class="p-6 pb-2 shrink-0 bg-white z-10 flex justify-between items-center">
                <h2 class="text-xl font-black text-slate-800">Konfirmasi Pesanan</h2>
                <button wire:click="closeCheckout" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <div class="overflow-y-auto px-6 py-2 flex-1 custom-scrollbar">
                <div class="space-y-3 mb-6">
                    @foreach($cart as $uuid => $item)
                    <div class="flex gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-100 items-start relative group">
                        <div class="flex flex-col gap-1 items-center">
                            <button wire:click="incrementQty('{{ $uuid }}')" class="w-6 h-6 bg-white border border-slate-200 rounded flex items-center justify-center text-xs font-bold hover:bg-indigo-50 text-indigo-600">+</button>
                            <span class="text-sm font-black text-slate-800">{{ $item['qty'] }}</span>
                            <button wire:click="decrementQty('{{ $uuid }}')" class="w-6 h-6 bg-white border border-slate-200 rounded flex items-center justify-center text-xs font-bold hover:bg-red-50 text-red-500">-</button>
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm line-clamp-1">{{ $item['name'] }}</h4>
                            <p class="text-xs text-slate-400">@ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            
                            {{-- Tampilkan Varian --}}
                            @if(!empty($item['variants']))
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($item['variants'] as $v)
                                        <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100">
                                            {{ $v['option_name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="font-bold text-slate-800 text-sm">
                            Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- FORM & TOTAL (Sama seperti sebelumnya) --}}
                <div class="border-t border-slate-100 my-4"></div>
                
                <div class="space-y-4 pb-4">
                    <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}</span>
                        </div>
                        @if($discountAmount > 0)
                        <div class="flex justify-between items-center text-xs text-emerald-600 font-black">
                            <span>Promo: {{ $appliedPromoName }}</span>
                            <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($this->getPointDiscount() > 0)
                        <div class="flex justify-between items-center text-xs text-orange-600 font-black">
                            <span>Potongan Poin</span>
                            <span>- Rp {{ number_format($this->getPointDiscount(), 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>Service Charge ({{ $serviceRate }}%)</span>
                            <span>Rp {{ number_format($this->getServiceCharge(), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-500">
                            <span>Tax ({{ $taxRate }}%)</span>
                            <span>Rp {{ number_format($this->getTaxAmount(), 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-slate-200 my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-800 text-sm">Grand Total</span>
                            <span class="font-black text-indigo-600 text-xl">Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Form Input --}}
                    <div class="space-y-3">
                        <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 mb-2">
                            <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2">Punya Member? (Masukkan No. WA)</label>
                            <input wire:model.live="memberPhone" type="number" placeholder="0812xxxx" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 font-bold text-slate-800 py-2.5 text-sm">
                            @if($isMember)
                                <div class="mt-3 p-3 bg-white rounded-xl border border-indigo-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-[10px] text-slate-500 font-bold uppercase">Saldo Poin</span>
                                        <span class="text-xs font-black text-indigo-600">{{ $memberPoints }} Poin</span>
                                    </div>
                                    
                                    @if($loyaltyEnabled && $memberPoints > 0)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <label class="text-[9px] text-slate-400 font-bold block mb-1">Pakai Poin</label>
                                            <input wire:model.live="pointsToRedeem" type="number" max="{{ $memberPoints }}" min="0" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-black text-slate-800 focus:ring-orange-500">
                                        </div>
                                        <div class="pt-4">
                                            <span class="text-[10px] font-bold text-orange-600">= -Rp {{ number_format($this->getPointDiscount(), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <p class="text-[8px] text-slate-400 mt-1 italic">*1 Poin = Rp {{ number_format($pointRedeemValue, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            @elseif(strlen($memberPhone) >= 10)
                                <p class="text-[10px] text-indigo-500 font-bold mt-2">Nomor baru? Otomatis jadi member & dapat poin!</p>
                            @endif
                            @error('memberPhone') <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <input wire:model="customerName" type="text" placeholder="Nama Pemesan (Wajib)" class="w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 font-bold text-slate-800 py-3.5 text-sm">
                            @error('customerName') <span class="text-red-500 text-xs font-bold ml-1 block">{{ $message }}</span> @enderror
                        </div>
                        <textarea wire:model="orderNote" rows="2" placeholder="Catatan Tambahan (Opsional)" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3.5 text-sm"></textarea>
                    </div>

                    {{-- Pilihan Pembayaran --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 mt-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer relative">
                                <input type="radio" wire:model.live="paymentMethod" value="cashier" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-center h-full hover:border-slate-200">
                                    <span class="text-2xl block mb-1">💵</span>
                                    <span class="text-xs font-bold block">Bayar di Kasir</span>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" wire:model.live="paymentMethod" value="midtrans" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-center h-full hover:border-slate-200">
                                    <span class="text-2xl block mb-1">💳</span>
                                    <span class="text-xs font-bold block">Otomatis / E-Wallet</span>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" wire:model.live="paymentMethod" value="qris" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-center h-full hover:border-slate-200">
                                    <span class="text-2xl block mb-1">📱</span>
                                    <span class="text-xs font-bold block">QRIS Manual</span>
                                </div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" wire:model.live="paymentMethod" value="transfer" class="peer sr-only">
                                <div class="p-3 rounded-xl border-2 border-slate-100 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all text-center h-full hover:border-slate-200">
                                    <span class="text-2xl block mb-1">🏦</span>
                                    <span class="text-xs font-bold block">Transfer Bank</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    @if($paymentMethod == 'midtrans')
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 text-center animate-pulse">
                        <p class="text-xs font-bold text-indigo-600">Pembayaran akan diverifikasi otomatis oleh sistem.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="p-6 pt-4 shrink-0 bg-white border-t border-slate-50 z-10">
                @error('checkout_error')
                    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg mb-3 text-xs text-red-600">{{ $message }}</div>
                @enderror

                <button wire:click="processCheckout" wire:loading.attr="disabled" class="w-full bg-slate-900 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-slate-800 active:scale-95 transition-all flex justify-center items-center gap-2">
                    <span wire:loading.remove>🚀 Lanjutkan Pembayaran</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL QRIS / TRANSFER --}}
    @if($isQrisModalOpen)
    <div class="fixed inset-0 z-[125] flex items-center justify-center bg-slate-900/70 backdrop-blur-sm px-4 animate-fade-in">
        <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl animate-slide-up flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="font-black text-lg text-slate-800">
                        {{ $paymentMethod == 'qris' ? 'Scan QRIS' : 'Transfer Bank' }}
                    </h3>
                    <p class="text-[11px] text-slate-400 font-bold">Selesaikan pembayaran untuk memproses pesanan</p>
                </div>
                <button wire:click="$set('isQrisModalOpen', false)" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-slate-500 shadow-sm border border-slate-200 hover:bg-slate-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 flex flex-col items-center">
                @if($paymentMethod == 'qris')
                    <p class="text-xs text-slate-500 mb-3 text-center">Silakan scan kode QR di bawah ini dengan aplikasi M-Banking atau E-Wallet pilihan Anda (GoPay, OVO, Dana, BCA Mobile, dll).</p>
                    @php
                        $qrisSrc = $qrisImage ? asset('storage/' . $qrisImage) : (file_exists(public_path('qris.jpg')) ? asset('qris.jpg') : null);
                    @endphp
                    <div class="bg-white p-3 rounded-2xl mb-4 border border-slate-200 shadow-md flex items-center justify-center">
                        @if($qrisSrc)
                            <img src="{{ $qrisSrc }}" alt="QRIS Pembayaran" class="w-52 h-52 object-contain rounded-xl">
                        @else
                            <div class="w-52 h-52 bg-slate-100 rounded-xl flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                <span class="text-3xl mb-2">📱</span>
                                <span class="text-xs font-bold">QRIS belum diunggah admin</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-slate-500 mb-3 text-center">Silakan transfer sesuai total tagihan ke rekening restoran berikut:</p>
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 w-full p-4 rounded-2xl mb-4 text-center shadow-xs">
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block mb-1">{{ $bankName ?? 'Bank BCA' }}</span>
                        <div class="flex items-center justify-center gap-2 my-1">
                            <span class="text-xl font-black text-slate-800 tracking-wider select-all">{{ $bankAccountNumber ?? '123 456 7890' }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-600 block mt-1">{{ $bankAccountName ?? 'a.n. Kedai Robby' }}</span>
                    </div>
                @endif
                
                <div class="w-full bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center mb-5">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block mb-1">Total yang Harus Dibayar</span>
                    <span class="text-2xl font-black text-indigo-600">Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                </div>

                <div class="w-full space-y-2">
                    <button wire:click="submitOrder" wire:loading.attr="disabled" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3.5 rounded-xl shadow-lg shadow-indigo-600/30 flex justify-center items-center gap-2 active:scale-95 transition-all">
                        <span wire:loading.remove>✅ Saya Sudah Bayar / Transfer</span>
                        <span wire:loading>Memproses Pesanan...</span>
                    </button>
                    <button wire:click="$set('isQrisModalOpen', false)" type="button" class="w-full py-2.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                        Ganti Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL SUKSES (Sama seperti sebelumnya) --}}
    @if(session()->has('success'))
    <div class="fixed inset-0 z-[120] flex items-center justify-center bg-white/95 backdrop-blur-md px-6 animate-fade-in">
        <div class="text-center w-full max-w-sm">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-800 mb-2">Berhasil!</h2>
            <p class="text-slate-500 text-sm mb-8 px-4">{{ session('success') }}</p>
            <button onclick="window.location.reload()" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-slate-800 transition-colors shadow-lg">Pesan Lagi</button>
        </div>
    </div>
    @endif
</div>

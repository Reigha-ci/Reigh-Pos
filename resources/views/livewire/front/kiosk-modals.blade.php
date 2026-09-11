<div>
    {{-- MODAL VARIAN --}}
    @if($isVariantModalOpen && $selectedProduct)
    <div class="fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div wire:click="$set('isVariantModalOpen', false)"
             class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>
        <div class="relative z-10 w-full max-w-lg rounded-[2rem] overflow-hidden shadow-2xl flex flex-col max-h-[85vh]"
             style="background:#1a1d27; border:1.5px solid #ffffff0f;">

            {{-- Header --}}
            <div style="padding:24px 28px; border-bottom:1px solid #ffffff0a; background:#13161f;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <h3 style="font-size:20px; font-weight:900; color:#f1f5f9; margin:0 0 4px; letter-spacing:-.02em;">
                            {{ $selectedProduct->name }}
                        </h3>
                        <p style="font-size:11px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.1em; margin:0;">
                            Sesuaikan Pesanan Anda
                        </p>
                    </div>
                    <button wire:click="$set('isVariantModalOpen', false)"
                            style="width:36px; height:36px; border-radius:12px; border:1px solid #ffffff15;
                                   background:#ffffff08; display:flex; align-items:center; justify-content:center;
                                   color:#64748b; cursor:pointer; flex-shrink:0; margin-left:12px;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Options --}}
            <div style="padding:24px 28px; overflow-y:auto; display:flex; flex-direction:column; gap:24px;"
                 class="kiosk-scroll">
                @foreach($selectedProduct->variants as $variant)
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h4 style="font-size:13px; font-weight:800; color:#e2e8f0; text-transform:uppercase;
                                    letter-spacing:.08em; margin:0;">{{ $variant->name }}</h4>
                        @if($variant->is_required)
                            <span style="font-size:9px; font-weight:800; background:#ef4444; color:#fff;
                                         padding:3px 10px; border-radius:99px; text-transform:uppercase; letter-spacing:.1em;">
                                Wajib
                            </span>
                        @endif
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        @foreach($variant->options as $option)
                        @php
                            $isSelected = ($variant->type == 'radio' && isset($selectedVariants[$variant->id]) && $selectedVariants[$variant->id] == $option->id) ||
                                          ($variant->type == 'checkbox' && isset($selectedVariants[$variant->id][$option->id]) && $selectedVariants[$variant->id][$option->id]);
                        @endphp
                        <label style="display:flex; flex-direction:column; padding:14px; border-radius:16px; cursor:pointer;
                                      border: 1.5px solid {{ $isSelected ? '#6366f1' : '#ffffff0f' }};
                                      background: {{ $isSelected ? '#6366f115' : '#13161f' }};
                                      transition: all .15s ease; position:relative;">
                            @if($variant->type == 'radio')
                                <input type="radio" wire:model.live="selectedVariants.{{ $variant->id }}" value="{{ $option->id }}" class="sr-only">
                            @else
                                <input type="checkbox" wire:model.live="selectedVariants.{{ $variant->id }}.{{ $option->id }}" class="sr-only">
                            @endif

                            <span style="font-size:13px; font-weight:800; color:{{ $isSelected ? '#818cf8' : '#e2e8f0' }}; margin-bottom:4px;">
                                {{ $option->name }}
                            </span>
                            @if($option->price > 0)
                                <span style="font-size:12px; font-weight:700; color:#6366f1;">
                                    +Rp {{ number_format($option->price, 0, ',', '.') }}
                                </span>
                            @endif

                            {{-- Check indicator --}}
                            <div style="position:absolute; top:12px; right:12px; width:20px; height:20px; border-radius:50%;
                                        border: 2px solid {{ $isSelected ? '#6366f1' : '#ffffff20' }};
                                        background: {{ $isSelected ? '#6366f1' : 'transparent' }};
                                        display:flex; align-items:center; justify-content:center;">
                                @if($isSelected)
                                <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @error('variant_error')
                    <div style="background:#ef444415; border:1px solid #ef444430; color:#f87171;
                                padding:14px 18px; border-radius:14px; font-size:13px; font-weight:700; text-align:center;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Action --}}
            <div style="padding:20px 28px; border-top:1px solid #ffffff0a; background:#13161f;">
                <button wire:click="saveVariantToCart"
                        style="width:100%; padding:16px 24px; border-radius:18px; border:none; cursor:pointer;
                               background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff;
                               font-size:15px; font-weight:900; letter-spacing:.04em;
                               display:flex; justify-content:space-between; align-items:center;
                               box-shadow:0 8px 28px #6366f150; transition:all .2s ease;">
                    <span>TAMBAH KE PESANAN</span>
                    <span>Rp {{ number_format($currentPrice, 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL CHECKOUT KIOSK --}}
    @if($isCheckoutOpen)
    <div class="fixed inset-0 z-[130] flex items-center justify-center p-4">
        <div wire:click="closeCheckout" class="absolute inset-0 bg-black/90 backdrop-blur-xl"></div>
        <div class="relative z-10 w-full max-w-xl rounded-[2rem] overflow-hidden shadow-2xl flex flex-col max-h-[90vh]"
             style="background:#1a1d27; border:1.5px solid #ffffff0f;">

            {{-- Header --}}
            <div style="padding:24px 28px 20px; border-bottom:1px solid #ffffff0a; background:#13161f;
                        display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h2 style="font-size:22px; font-weight:900; color:#f1f5f9; letter-spacing:-.03em; margin:0 0 4px;">
                        Konfirmasi Pesanan
                    </h2>
                    <p style="font-size:11px; color:#475569; font-weight:600; margin:0;">
                        Lengkapi detail sebelum memesan
                    </p>
                </div>
                <button wire:click="closeCheckout"
                        style="width:36px; height:36px; border-radius:12px; border:1px solid #ffffff15;
                               background:#ffffff08; display:flex; align-items:center; justify-content:center;
                               color:#64748b; cursor:pointer; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Content --}}
            <div style="padding:24px 28px; overflow-y:auto; display:flex; flex-direction:column; gap:20px;"
                 class="kiosk-scroll">

                {{-- Nama --}}
                <div>
                    <label style="display:block; font-size:10px; font-weight:800; color:#475569;
                                  text-transform:uppercase; letter-spacing:.15em; margin-bottom:8px;">
                        Nama Anda
                    </label>
                    <input wire:model="customerName" type="text" placeholder="Masukkan nama panggilan..."
                           style="width:100%; background:#13161f; border:1.5px solid #ffffff0f; border-radius:16px;
                                  color:#f1f5f9; font-size:16px; font-weight:700; padding:14px 18px;
                                  outline:none; transition:all .2s ease;"
                           onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#ffffff0f'">
                    @error('customerName')
                        <span style="color:#f87171; font-size:12px; font-weight:600; margin-top:6px; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Metode Pembayaran --}}
                <div>
                    <label style="display:block; font-size:10px; font-weight:800; color:#475569;
                                  text-transform:uppercase; letter-spacing:.15em; margin-bottom:10px;">
                        Metode Pembayaran
                    </label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

                        {{-- Kasir --}}
                        <label style="cursor:pointer; display:block;"
                               wire:click="$set('paymentMethod','cashier')">
                            <div style="padding:20px 16px; border-radius:18px; text-align:center;
                                        transition:all .2s ease; position:relative;
                                        {{ $paymentMethod === 'cashier'
                                            ? 'border:2px solid #6366f1; background:linear-gradient(135deg,#6366f115,#8b5cf615);'
                                            : 'border:1.5px solid #ffffff12; background:#13161f;' }}">
                                @if($paymentMethod === 'cashier')
                                    <div style="position:absolute; top:10px; right:10px; width:20px; height:20px;
                                                border-radius:50%; background:#6366f1; display:flex;
                                                align-items:center; justify-content:center;">
                                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <div style="font-size:36px; margin-bottom:10px;">💵</div>
                                <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
                                            color: {{ $paymentMethod === 'cashier' ? '#818cf8' : '#94a3b8' }};">
                                    Bayar di Kasir
                                </div>
                            </div>
                        </label>

                        {{-- QRIS / Online --}}
                        <label style="cursor:pointer; display:block;"
                               wire:click="$set('paymentMethod','midtrans')">
                            <div style="padding:20px 16px; border-radius:18px; text-align:center;
                                        transition:all .2s ease; position:relative;
                                        {{ $paymentMethod === 'midtrans'
                                            ? 'border:2px solid #6366f1; background:linear-gradient(135deg,#6366f115,#8b5cf615);'
                                            : 'border:1.5px solid #ffffff12; background:#13161f;' }}">
                                @if($paymentMethod === 'midtrans')
                                    <div style="position:absolute; top:10px; right:10px; width:20px; height:20px;
                                                border-radius:50%; background:#6366f1; display:flex;
                                                align-items:center; justify-content:center;">
                                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <div style="font-size:36px; margin-bottom:10px;">💳</div>
                                <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
                                            color: {{ $paymentMethod === 'midtrans' ? '#818cf8' : '#94a3b8' }};">
                                    Otomatis / E-Wallet
                                </div>
                            </div>
                        </label>

                        {{-- QRIS Manual --}}
                        <label style="cursor:pointer; display:block;"
                               wire:click="$set('paymentMethod','qris')">
                            <div style="padding:20px 16px; border-radius:18px; text-align:center;
                                        transition:all .2s ease; position:relative;
                                        {{ $paymentMethod === 'qris'
                                            ? 'border:2px solid #6366f1; background:linear-gradient(135deg,#6366f115,#8b5cf615);'
                                            : 'border:1.5px solid #ffffff12; background:#13161f;' }}">
                                @if($paymentMethod === 'qris')
                                    <div style="position:absolute; top:10px; right:10px; width:20px; height:20px;
                                                border-radius:50%; background:#6366f1; display:flex;
                                                align-items:center; justify-content:center;">
                                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <div style="font-size:36px; margin-bottom:10px;">📱</div>
                                <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
                                            color: {{ $paymentMethod === 'qris' ? '#818cf8' : '#94a3b8' }};">
                                    QRIS Manual
                                </div>
                            </div>
                        </label>

                        {{-- Transfer Bank --}}
                        <label style="cursor:pointer; display:block;"
                               wire:click="$set('paymentMethod','transfer')">
                            <div style="padding:20px 16px; border-radius:18px; text-align:center;
                                        transition:all .2s ease; position:relative;
                                        {{ $paymentMethod === 'transfer'
                                            ? 'border:2px solid #6366f1; background:linear-gradient(135deg,#6366f115,#8b5cf615);'
                                            : 'border:1.5px solid #ffffff12; background:#13161f;' }}">
                                @if($paymentMethod === 'transfer')
                                    <div style="position:absolute; top:10px; right:10px; width:20px; height:20px;
                                                border-radius:50%; background:#6366f1; display:flex;
                                                align-items:center; justify-content:center;">
                                        <svg width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                                <div style="font-size:36px; margin-bottom:10px;">🏦</div>
                                <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
                                            color: {{ $paymentMethod === 'transfer' ? '#818cf8' : '#94a3b8' }};">
                                    Transfer Bank
                                </div>
                            </div>
                        </label>

                    </div>
                </div>


                {{-- Rincian Harga --}}
                <div style="background:#13161f; border-radius:18px; padding:18px;
                            border:1.5px solid #ffffff0a; display:flex; flex-direction:column; gap:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; color:#64748b; font-weight:600;">Subtotal</span>
                        <span style="font-size:13px; color:#94a3b8; font-weight:700;">
                            Rp {{ number_format($this->getSubtotal(), 0, ',', '.') }}
                        </span>
                    </div>
                    @if($discountAmount > 0)
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:13px; color:#34d399; font-weight:700;">Diskon ({{ $appliedPromoName }})</span>
                        <span style="font-size:13px; color:#34d399; font-weight:700;">
                            - Rp {{ number_format($discountAmount, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; color:#475569; font-weight:600;">Service ({{ $serviceRate }}%)</span>
                        <span style="font-size:12px; color:#64748b; font-weight:600;">
                            Rp {{ number_format($this->getServiceCharge(), 0, ',', '.') }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; color:#475569; font-weight:600;">Pajak ({{ $taxRate }}%)</span>
                        <span style="font-size:12px; color:#64748b; font-weight:600;">
                            Rp {{ number_format($this->getTaxAmount(), 0, ',', '.') }}
                        </span>
                    </div>
                    <div style="border-top:1px solid #ffffff0a; padding-top:12px; margin-top:4px;
                                display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:14px; font-weight:900; color:#f1f5f9; text-transform:uppercase;
                                     letter-spacing:.04em;">Total Bayar</span>
                        <span style="font-size:22px; font-weight:900; color:#818cf8; letter-spacing:-.03em;">
                            Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div style="padding:20px 28px; border-top:1px solid #ffffff0a; background:#13161f;">
                <button wire:click="processCheckout" wire:loading.attr="disabled"
                        style="width:100%; padding:18px; border-radius:18px; border:none; cursor:pointer;
                               background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff;
                               font-size:16px; font-weight:900; letter-spacing:.04em; text-transform:uppercase;
                               box-shadow:0 8px 32px #6366f155; transition:all .25s ease;
                               display:flex; align-items:center; justify-content:center; gap:10px;">
                    <span wire:loading.remove>🔥 Lanjutkan</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL QRIS / TRANSFER KIOSK --}}
    @if($isQrisModalOpen)
    <div class="fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div wire:click="$set('isQrisModalOpen', false)" class="absolute inset-0 bg-black/90 backdrop-blur-xl"></div>
        <div class="relative z-10 w-full max-w-sm rounded-[2rem] overflow-hidden shadow-2xl flex flex-col animate-slide-up"
             style="background:#1a1d27; border:1.5px solid #ffffff0f;">
            
            <div style="padding:24px 28px 20px; border-bottom:1px solid #ffffff0a; background:#13161f; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 style="font-size:18px; font-weight:900; color:#f1f5f9; margin:0;">
                        {{ $paymentMethod == 'qris' ? 'Scan QRIS' : 'Transfer Bank' }}
                    </h3>
                    <p style="font-size:11px; color:#64748b; font-weight:600; margin:4px 0 0;">
                        Selesaikan pembayaran pesanan Anda
                    </p>
                </div>
                <button wire:click="$set('isQrisModalOpen', false)" style="width:34px; height:34px; border-radius:12px; border:1px solid #ffffff15; background:#ffffff08; color:#94a3b8; cursor:pointer; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:900;">
                    ✕
                </button>
            </div>
            
            <div style="padding:24px 28px; display:flex; flex-direction:column; align-items:center;">
                @if($paymentMethod == 'qris')
                    <p style="font-size:12px; color:#94a3b8; text-align:center; margin-bottom:16px;">Silakan scan QRIS di bawah ini dengan E-Wallet / Mobile Banking Anda.</p>
                    @php
                        $kioskQris = $qrisImage ? asset('storage/' . $qrisImage) : (file_exists(public_path('qris.jpg')) ? asset('qris.jpg') : null);
                    @endphp
                    <div style="background:#fff; padding:12px; border-radius:18px; margin-bottom:16px; box-shadow:0 8px 24px rgba(0,0,0,.4);">
                        @if($kioskQris)
                            <img src="{{ $kioskQris }}" alt="QRIS" style="width:190px; height:190px; object-fit:contain; border-radius:8px;">
                        @else
                            <div style="width:190px; height:190px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#64748b; font-size:12px; font-weight:700; text-align:center;">
                                <span style="font-size:32px; margin-bottom:8px;">📱</span>
                                QRIS belum diunggah admin
                            </div>
                        @endif
                    </div>
                @else
                    <p style="font-size:12px; color:#94a3b8; text-align:center; margin-bottom:16px;">Silakan transfer sesuai nominal ke rekening berikut ini.</p>
                    <div style="background:#6366f115; border:1px solid #6366f130; width:100%; padding:18px; border-radius:18px; margin-bottom:16px; text-align:center;">
                        <span style="font-size:11px; font-weight:800; color:#818cf8; text-transform:uppercase; letter-spacing:.1em; display:block; margin-bottom:6px;">{{ $bankName ?? 'Bank BCA' }}</span>
                        <span style="font-size:22px; font-weight:900; color:#f1f5f9; display:block; letter-spacing:.05em;">{{ $bankAccountNumber ?? '123 456 7890' }}</span>
                        <span style="font-size:12px; font-weight:700; color:#94a3b8; display:block; margin-top:6px;">{{ $bankAccountName ?? 'a.n. Kedai Robby' }}</span>
                    </div>
                @endif
                
                <div style="width:100%; background:#13161f; border:1px solid #ffffff0a; padding:16px; border-radius:16px; text-align:center; margin-bottom:20px;">
                    <span style="font-size:10px; color:#64748b; font-weight:700; display:block; margin-bottom:4px; text-transform:uppercase; letter-spacing:.08em;">Total Tagihan</span>
                    <span style="font-size:22px; font-weight:900; color:#818cf8;">Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}</span>
                </div>

                <div style="width:100%; display:flex; flex-direction:column; gap:10px;">
                    <button wire:click="submitOrder" wire:loading.attr="disabled"
                            style="width:100%; padding:16px; border-radius:16px; border:none; cursor:pointer;
                                   background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff;
                                   font-size:14px; font-weight:900; letter-spacing:.04em; text-transform:uppercase;
                                   box-shadow:0 8px 32px #6366f155; transition:all .25s ease;">
                        <span wire:loading.remove>✅ Saya Sudah Bayar / Transfer</span>
                        <span wire:loading>Memproses Pesanan...</span>
                    </button>
                    <button wire:click="$set('isQrisModalOpen', false)" type="button"
                            style="width:100%; background:transparent; border:none; color:#94a3b8; font-size:12px; font-weight:700; cursor:pointer; padding:8px;">
                        Ganti Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SUCCESS MODAL KIOSK --}}
    @if(session()->has('success'))
    <div class="fixed inset-0 z-[200] flex items-center justify-center px-6"
         style="background:linear-gradient(135deg,#1e1b4b,#312e81,#4c1d95);">
        <div style="text-align:center; color:#fff; max-width:400px; width:100%;">
            <div style="width:120px; height:120px; background:rgba(255,255,255,.12); border-radius:50%;
                        display:flex; align-items:center; justify-content:center; margin:0 auto 28px;
                        animation:bounce 1s infinite; border:2px solid rgba(255,255,255,.2);">
                <svg width="56" height="56" fill="none" stroke="white" stroke-width="3.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 style="font-size:48px; font-weight:900; letter-spacing:-.04em; margin:0 0 12px; text-transform:uppercase;">
                BERHASIL!
            </h2>
            <p style="font-size:16px; font-weight:500; opacity:.75; margin:0 0 40px; line-height:1.5;">
                {{ session('success') }}
            </p>
            <button onclick="window.location.reload()"
                    style="width:100%; background:#fff; color:#4c1d95; padding:20px; border-radius:20px;
                           font-size:18px; font-weight:900; border:none; cursor:pointer;
                           box-shadow:0 8px 32px rgba(0,0,0,.3); letter-spacing:.04em; text-transform:uppercase;
                           transition:all .2s ease;">
                PESAN LAGI
            </button>
        </div>
    </div>
    @endif
</div>

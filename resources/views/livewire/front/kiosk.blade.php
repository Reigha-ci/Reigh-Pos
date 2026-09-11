<div class="kiosk-wrap" x-data="{ cartPulse: false }" style="display:flex; height:100dvh; overflow:hidden; background:#0f1117; font-family:'Inter',sans-serif;">
    @php
        $tenant    = $table->tenant ?? null;
        $storeName = $tenant->name ?? \App\Models\Setting::value('store_name', 'KedaiRobby.id');
        $storeLogo = $tenant->logo ?? \App\Models\Setting::value('store_logo');
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');

        .kiosk-wrap * { box-sizing: border-box; }

        /* ── Scrollbar ── */
        .kiosk-scroll::-webkit-scrollbar { width: 4px; }
        .kiosk-scroll::-webkit-scrollbar-track { background: transparent; }
        .kiosk-scroll::-webkit-scrollbar-thumb { background: #ffffff18; border-radius: 99px; }

        /* ── Category sidebar ── */
        .cat-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            width: 76px; height: 76px; border-radius: 20px; gap: 6px; border: none; cursor: pointer;
            transition: all .2s cubic-bezier(.34,1.56,.64,1); background: transparent; color: #94a3b8;
        }
        .cat-btn:hover { background: #1e2230; transform: scale(1.05); }
        .cat-btn.active { background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff;
            box-shadow: 0 8px 24px #6366f155; transform: scale(1.08); }
        .cat-btn span.label { font-size: 9px; font-weight: 800; text-transform: uppercase;
            letter-spacing: .08em; text-align: center; line-height: 1.2; }

        /* ── Product card ── */
        .prod-card {
            background: #1a1d27; border-radius: 24px; overflow: hidden; cursor: pointer;
            border: 1.5px solid #ffffff0d; transition: all .25s cubic-bezier(.34,1.56,.64,1);
            display: flex; flex-direction: column; position: relative;
        }
        .prod-card:hover { transform: translateY(-6px) scale(1.02);
            border-color: #6366f155; box-shadow: 0 24px 48px #00000060; }
        .prod-card:active { transform: scale(.97); }
        .prod-card .img-wrap { position: relative; padding-top: 75%; overflow: hidden; background: #12151e; }
        .prod-card .img-wrap img { position: absolute; inset:0; width:100%; height:100%; object-fit:cover;
            transition: transform .5s ease; }
        .prod-card:hover .img-wrap img { transform: scale(1.08); }
        .prod-card .add-btn {
            position: absolute; bottom: 12px; right: 12px; width: 38px; height: 38px;
            background: linear-gradient(135deg,#6366f1,#8b5cf6); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
            color: #fff; font-weight: 900; box-shadow: 0 4px 14px #6366f160;
            opacity:0; transform: scale(.7); transition: all .2s ease;
        }
        .prod-card:hover .add-btn { opacity:1; transform: scale(1); }
        .prod-card .info { padding: 14px 16px 16px; flex:1; }
        .prod-card .info h3 { font-size: 14px; font-weight: 800; color: #f1f5f9;
            line-height: 1.3; margin: 0 0 6px; }
        .prod-card .info .price { font-size: 16px; font-weight: 900; color: #818cf8; }
        .prod-card .badge-variant {
            position: absolute; top: 10px; left: 10px; font-size: 9px; font-weight: 800;
            background: linear-gradient(135deg,#6366f1,#8b5cf6); color: #fff;
            padding: 3px 10px; border-radius: 99px; text-transform: uppercase; letter-spacing: .1em;
        }
        .prod-card .no-img-ph { position:absolute; inset:0; display:flex; align-items:center;
            justify-content:center; color: #ffffff12; }

        /* ── Cart item ── */
        .cart-item { background: #1a1d27; border-radius: 18px; padding: 14px;
            border: 1.5px solid #ffffff0d; display: flex; align-items: flex-start; gap: 12px;
            transition: all .2s ease; }
        .cart-item:hover { border-color: #6366f133; }
        .qty-btn { width:30px; height:30px; border-radius: 10px; border:none; cursor:pointer;
            font-weight: 900; font-size: 16px; display:flex; align-items:center; justify-content:center;
            transition: all .15s ease; }
        .qty-btn.plus { background: #6366f122; color: #818cf8; }
        .qty-btn.plus:hover { background: #6366f1; color: #fff; }
        .qty-btn.minus { background: #ef444420; color: #f87171; }
        .qty-btn.minus:hover { background: #ef4444; color: #fff; }

        /* ── Pulse animation for cart badge ── */
        @keyframes pulse-badge {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        .pulse { animation: pulse-badge .4s ease; }
    </style>

    {{-- ═══ SIDEBAR KIRI: KATEGORI ═══ --}}
    <div style="width:100px; flex-shrink:0; background:#13161f; border-right:1px solid #ffffff08;
                display:flex; flex-direction:column; align-items:center; padding:20px 0; gap:12px;
                overflow-y:auto;" class="kiosk-scroll">

        {{-- Logo --}}
        <div style="margin-bottom:12px; padding:0 12px;">
            @if($storeLogo)
                <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}"
                     style="width:56px; height:56px; border-radius:16px; object-fit:cover;
                            border:2px solid #6366f140; box-shadow:0 4px 14px #6366f130;">
            @else
                <div style="width:56px; height:56px; border-radius:16px;
                            background:linear-gradient(135deg,#6366f1,#8b5cf6);
                            display:flex; align-items:center; justify-content:center;
                            font-size:22px; font-weight:900; color:#fff;
                            box-shadow:0 4px 14px #6366f140;">
                    {{ strtoupper(substr($storeName,0,1)) }}
                </div>
            @endif
        </div>

        {{-- Semua --}}
        <button wire:click="setCategory('all')"
                class="cat-btn {{ $activeCategory === 'all' ? 'active' : '' }}">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="label">Semua</span>
        </button>

        @foreach($categories as $cat)
        <button wire:click="setCategory({{ $cat->id }})"
                class="cat-btn {{ $activeCategory == $cat->id ? 'active' : '' }}">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="label">{{ Str::limit($cat->name, 8) }}</span>
        </button>
        @endforeach
    </div>

    {{-- ═══ TENGAH: PRODUK ═══ --}}
    <div style="flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0;">

        {{-- Header --}}
        <header style="height:72px; background:#13161f; border-bottom:1px solid #ffffff08;
                       display:flex; align-items:center; justify-content:space-between;
                       padding:0 28px; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:14px;">
                @if($storeLogo)
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}"
                         style="width:40px; height:40px; border-radius:12px; object-fit:cover;
                                border:1.5px solid #6366f140;">
                @endif
                <div>
                    <h1 style="font-size:18px; font-weight:900; color:#f1f5f9; letter-spacing:-.03em;
                               line-height:1.1; margin:0;">
                        {{ $storeName }}
                        <span style="color:#818cf8;">Kiosk</span>
                    </h1>
                    <p style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase;
                               letter-spacing:.12em; margin:2px 0 0;">{{ $table_name }}</p>
                </div>
            </div>

            <button wire:click="callWaitress"
                    style="display:flex; align-items:center; gap:8px; padding:10px 20px;
                           border-radius:14px; border:1.5px solid #ef444430; background:#ef44440d;
                           color:#f87171; font-size:12px; font-weight:800; cursor:pointer;
                           text-transform:uppercase; letter-spacing:.1em;
                           transition:all .2s ease;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                     viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Panggil Pelayan
            </button>
        </header>

        {{-- Product Grid --}}
        <main style="flex:1; overflow-y:auto; padding:24px;" class="kiosk-scroll">
            @if($products->isEmpty())
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center;
                            height:100%; color:#334155; text-align:center; padding:40px;">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5"
                         viewBox="0 0 24 24" style="margin-bottom:16px; opacity:.3;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p style="font-size:16px; font-weight:800; text-transform:uppercase; letter-spacing:.1em;">
                        Menu tidak tersedia
                    </p>
                </div>
            @else
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(175px,1fr)); gap:16px;">
                    @foreach($products as $product)
                    <div wire:key="prod-{{ $product->id }}"
                         wire:click="addToCart({{ $product->id }})"
                         class="prod-card">
                        <div class="img-wrap">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-img-ph">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2"
                                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            @if($product->variants->isNotEmpty())
                                <div class="badge-variant">Pilihan</div>
                            @endif
                            <div class="add-btn">+</div>
                        </div>
                        <div class="info">
                            <h3>{{ $product->name }}</h3>
                            <p class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

    {{-- ═══ KANAN: CART ═══ --}}
    <div style="width:340px; flex-shrink:0; background:#13161f; border-left:1px solid #ffffff08;
                display:flex; flex-direction:column;">

        {{-- Cart Header --}}
        <div style="padding:20px 20px 16px; border-bottom:1px solid #ffffff08;
                    display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:linear-gradient(135deg,#6366f1,#8b5cf6);
                            border-radius:12px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5"
                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h2 style="font-size:16px; font-weight:900; color:#f1f5f9; letter-spacing:-.02em; margin:0;">
                    Pesanan Anda
                </h2>
            </div>
            <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff;
                        width:28px; height:28px; border-radius:50%; display:flex; align-items:center;
                        justify-content:center; font-size:13px; font-weight:900;
                        box-shadow:0 4px 12px #6366f140;">
                {{ $this->getTotalItems() }}
            </div>
        </div>

        {{-- Cart Items --}}
        <div style="flex:1; overflow-y:auto; padding:14px; display:flex; flex-direction:column; gap:10px;"
             class="kiosk-scroll">
            @forelse($cart as $uuid => $item)
            <div wire:key="cart-{{ $uuid }}" class="cart-item">
                <div style="flex:1; min-width:0;">
                    <h4 style="font-size:13px; font-weight:800; color:#e2e8f0; margin:0 0 4px;
                                line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $item['name'] }}
                    </h4>
                    <p style="font-size:12px; color:#6366f1; font-weight:700; margin:0 0 6px;">
                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                    </p>
                    @if(!empty($item['variants']))
                        <div style="display:flex; flex-wrap:wrap; gap:4px;">
                            @foreach($item['variants'] as $v)
                                <span style="font-size:10px; background:#6366f115; border:1px solid #6366f130;
                                             color:#818cf8; padding:2px 8px; border-radius:6px; font-weight:700;">
                                    {{ $v['option_name'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; flex-shrink:0;">
                    <button wire:click="incrementQty('{{ $uuid }}')" class="qty-btn plus">+</button>
                    <span style="font-size:15px; font-weight:900; color:#f1f5f9; min-width:20px;
                                 text-align:center;">{{ $item['qty'] }}</span>
                    <button wire:click="decrementQty('{{ $uuid }}')" class="qty-btn minus">−</button>
                </div>
            </div>
            @empty
            <div style="flex:1; display:flex; flex-direction:column; align-items:center;
                        justify-content:center; padding:40px 20px; text-align:center; opacity:.3;">
                <svg width="56" height="56" fill="none" stroke="#6366f1" stroke-width="1.5"
                     viewBox="0 0 24 24" style="margin-bottom:14px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p style="font-size:12px; font-weight:800; color:#94a3b8;
                           text-transform:uppercase; letter-spacing:.1em;">Keranjang Kosong</p>
                <p style="font-size:11px; color:#475569; margin-top:6px; font-weight:500;">
                    Pilih menu di sebelah kiri
                </p>
            </div>
            @endforelse
        </div>

        {{-- Cart Footer --}}
        <div style="padding:16px; border-top:1px solid #ffffff08; background:#0f1117;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:11px; font-weight:800; color:#475569;
                             text-transform:uppercase; letter-spacing:.1em;">Total Bayar</span>
                <span style="font-size:24px; font-weight:900; color:#818cf8; letter-spacing:-.03em;">
                    Rp {{ number_format($this->getGrandTotal(), 0, ',', '.') }}
                </span>
            </div>
            <button wire:click="openCheckout" @disabled(empty($cart))
                    style="width:100%; padding:16px; border-radius:18px; border:none; cursor:pointer;
                           background: {{ empty($cart) ? '#1e2230' : 'linear-gradient(135deg,#6366f1,#8b5cf6)' }};
                           color: {{ empty($cart) ? '#475569' : '#fff' }};
                           font-size:15px; font-weight:900; letter-spacing:.04em; text-transform:uppercase;
                           box-shadow: {{ empty($cart) ? 'none' : '0 8px 28px #6366f150' }};
                           transition:all .25s ease; display:flex; align-items:center;
                           justify-content:center; gap:10px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"
                     viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Pesan Sekarang
            </button>
        </div>
    </div>

    {{-- REUSE MODAL DARI ORDER PAGE (Variant & Checkout) --}}
    @include('livewire.front.kiosk-modals')

</div>

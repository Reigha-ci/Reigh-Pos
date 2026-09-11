<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    
    <!-- Success Notification Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 mb-6 text-center relative overflow-hidden">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-sm shadow-emerald-500/10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-2">
            Pesanan Katering Berhasil Dibuat
        </span>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
            Terima Kasih, {{ $order->customer_name }}!
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed mb-6">
            Pesanan Anda telah kami catat dengan nomor <strong class="text-slate-800 font-bold">#CAT-{{ $order->id }}</strong>. Silakan konfirmasi melalui WhatsApp agar tim kami segera memproses jadwal acara Anda.
        </p>

        <!-- Big WhatsApp Action Button -->
        <a href="{{ $order->catering_whatsapp_url }}" target="_blank" class="inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm sm:text-base shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/40 transition-all w-full sm:w-auto">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
            </svg>
            <span>Konfirmasi Pesanan ke WhatsApp</span>
        </a>
    </div>

    <!-- Status Tracking Timeline -->
    <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-200/80 mb-6">
        <h3 class="font-black text-slate-900 text-sm uppercase tracking-wider mb-4">Status Pesanan Katering</h3>

        @php
            $currentStatus = $order->catering_status ?? $order->status;
            $steps = [
                'pending' => ['title' => 'Menunggu Konfirmasi', 'desc' => 'Pesanan diterima sistem'],
                'confirmed' => ['title' => 'Dikonfirmasi', 'desc' => 'Jadwal masuk dapur'],
                'cooking' => ['title' => 'Sedang Dimasak', 'desc' => 'Dipersiapkan oleh chef'],
                'delivering' => ['title' => 'Dalam Pengantaran', 'desc' => 'Menuju alamat Anda'],
                'completed' => ['title' => 'Pesanan Selesai', 'desc' => 'Sudah diterima'],
            ];
            $statusOrder = ['pending', 'confirmed', 'cooking', 'delivering', 'completed'];
            $currentIndex = array_search($currentStatus, $statusOrder);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
            @foreach($statusOrder as $idx => $stepKey)
                @php
                    $isPassed = $idx <= $currentIndex;
                    $isCurrent = $idx === $currentIndex;
                    $isRejected = $order->catering_status == 'rejected' && $isCurrent;
                @endphp
                <div class="flex flex-col items-center p-3 rounded-2xl {{ $isCurrent ? ($isRejected ? 'bg-rose-50 border-2 border-rose-500' : 'bg-amber-50 border-2 border-amber-500') : ($isPassed ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200/60 opacity-60') }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs mb-1.5 {{ $isCurrent ? ($isRejected ? 'bg-rose-500 text-white' : 'bg-amber-500 text-white') : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                        @if($isPassed && !$isCurrent)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        @elseif($isRejected)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="text-xs font-bold text-slate-800 leading-tight block">{{ $isRejected ? 'Pesanan Ditolak' : $steps[$stepKey]['title'] }}</span>
                    <span class="text-[10px] text-slate-500 mt-0.5 leading-tight hidden sm:block">{{ $isRejected ? 'Cek alasan di bawah' : $steps[$stepKey]['desc'] }}</span>
                </div>
            @endforeach
        </div>

        @if($order->catering_status == 'rejected')
            <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200">
                <h4 class="font-bold text-rose-800 text-sm mb-1">Pesanan Ditolak</h4>
                <p class="text-xs text-rose-700">{{ $order->rejection_reason ?? 'Maaf, pesanan katering Anda tidak dapat kami proses saat ini.' }}</p>
            </div>
        @endif
    </div>

    <!-- Invoice Details Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 mb-6">
        
        <!-- Header Invoice -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center pb-6 border-b border-slate-100 gap-4 mb-6">
            <div>
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 block mb-1">Invoice Katering</span>
                <h2 class="text-2xl font-black text-slate-900 leading-none">#CAT-{{ $order->id }}</h2>
                <p class="text-xs text-slate-400 mt-1">Dipesan pada: {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="sm:text-right">
                <span class="text-xs font-bold text-slate-500 block">Restoran / Katering:</span>
                <span class="font-extrabold text-sm text-slate-900 block">{{ $storeName }}</span>
                <span class="text-xs text-slate-400 block">{{ $storePhone }}</span>
            </div>
        </div>

        <!-- Detail Pengantaran Acara -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-6 text-xs">
            <div>
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block mb-1">Penerima & Kontak</span>
                <p class="font-extrabold text-slate-900 text-sm">{{ $order->customer_name }}</p>
                <p class="text-slate-600">{{ $order->customer_phone }}</p>
            </div>
            <div>
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block mb-1">Jadwal Acara & Pengantaran</span>
                <p class="font-extrabold text-amber-700 text-sm">
                    {{ $order->delivery_date ? $order->delivery_date->format('l, d F Y') : '-' }}
                </p>
                <p class="text-slate-700 font-semibold">Pukul: {{ $order->delivery_time ?? '-' }} WIB</p>
            </div>
            <div class="sm:col-span-2 pt-2 border-t border-slate-200/50">
                <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px] block mb-1">Alamat Lengkap Pengiriman</span>
                <p class="text-slate-800 leading-relaxed font-medium">{{ $order->delivery_address }}</p>
                @if($order->note)
                    <p class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-lg mt-2 border border-amber-200/50 font-medium">
                        <strong>Catatan Khusus:</strong> {{ $order->note }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Daftar Menu Item -->
        <div class="mb-6">
            <h4 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Rincian Menu Katering</h4>
            <div class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-start justify-between gap-4">
                        <div>
                            <h5 class="font-extrabold text-sm text-slate-900">{{ $item->product->name ?? 'Menu Katering' }}</h5>
                            <span class="text-xs text-slate-500 font-medium">{{ $item->quantity }} Porsi @ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            @if($item->selectedVariants && $item->selectedVariants->isNotEmpty())
                                <div class="mt-1 space-y-0.5">
                                    @foreach($item->selectedVariants as $variant)
                                        <span class="inline-block text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                            {{ $variant->variant_name }}: {{ $variant->option_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <span class="font-extrabold text-sm text-slate-900 shrink-0">
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Total Price Summary -->
        <div class="pt-4 border-t border-slate-200 space-y-2 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal Menu</span>
                <span class="font-bold text-slate-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Potongan Diskon</span>
                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-slate-600">
                <span>Metode Pembayaran</span>
                <span class="font-extrabold uppercase text-slate-800">{{ $order->payment_method }}</span>
            </div>
            <div class="flex justify-between text-base font-black text-slate-900 pt-3 border-t border-slate-200">
                <span>Total Biaya</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            
            @if($order->dp_amount)
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 mt-2 border-t border-slate-200">
                    <span>Uang Muka / DP Harus Dibayar:</span>
                    <span class="text-amber-600 text-xl">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($order->remaining_amount && $order->remaining_amount > 0)
                <div class="flex justify-between text-sm font-bold text-rose-600 mt-1">
                    <span>Sisa Pelunasan:</span>
                    <span>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }} (via {{ $order->remaining_payment_method == 'cod' ? 'COD / Bayar di Tempat' : 'Transfer Bank / QRIS' }})</span>
                </div>
            @endif
        </div>

    </div>

    <!-- Info Rekening Transfer & QRIS -->
    <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl mb-6 relative overflow-hidden">
        <div class="relative z-10">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-300 block mb-1">Tujuan Pembayaran DP</span>
            <h3 class="font-black text-xl mb-2">Rekening Bank & QRIS Restoran</h3>
            <p class="text-xs text-slate-300 mb-6 leading-relaxed">
                Silakan lakukan transfer sebesar <strong class="text-amber-400 font-bold">Rp {{ number_format($order->dp_amount ?: $order->total_price, 0, ',', '.') }}</strong> ke rekening atau QRIS resmi di bawah ini:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                {{-- Bank Info --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                    <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest block mb-1">{{ $bankName ?? 'Bank BCA' }}</span>
                    <span class="text-2xl font-black text-white tracking-wider block select-all my-1">{{ $bankAccountNumber ?? '123 456 7890' }}</span>
                    <span class="text-xs font-semibold text-slate-300 block">{{ $bankAccountName ?? 'a.n. Kedai Robby' }}</span>
                </div>

                {{-- QRIS Info --}}
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 flex items-center gap-4">
                    @php
                        $invQris = $qrisImage ? asset('storage/' . $qrisImage) : (file_exists(public_path('qris.jpg')) ? asset('qris.jpg') : null);
                    @endphp
                    @if($invQris)
                        <img src="{{ $invQris }}" alt="QRIS" class="w-20 h-20 bg-white p-1 rounded-xl object-contain shrink-0">
                    @else
                        <div class="w-20 h-20 bg-white/5 rounded-xl flex items-center justify-center text-slate-400 text-2xl shrink-0">
                            📱
                        </div>
                    @endif
                    <div>
                        <span class="text-xs font-black text-white block">Scan QRIS</span>
                        <span class="text-[10px] text-slate-300 block mt-1">Bisa melalui BCA Mobile, GoPay, OVO, ShopeePay, DANA, dll.</span>
                    </div>
                </div>
            </div>

            <!-- Fast WhatsApp Button -->
            <a href="{{ $order->catering_whatsapp_url }}" target="_blank" class="w-full inline-flex items-center justify-center gap-3 px-6 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm shadow-lg shadow-emerald-500/30 transition-all">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>📲 Kirim Bukti Transfer ke WhatsApp Admin</span>
            </a>
        </div>
    </div>

    <!-- Upload Bukti Transfer di Website -->
    @if(!$order->payment_proof)
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 mb-6">
        <h3 class="font-black text-slate-900 text-sm uppercase tracking-wider mb-2">Unggah Bukti Transfer di Sini</h3>
        <p class="text-xs text-slate-500 mb-4">Selain kirim via WhatsApp, Anda juga dapat mengunggah struk/foto bukti pembayaran langsung di bawah ini agar tersimpan di sistem.</p>
        
        <form wire:submit.prevent="uploadProof" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Foto Bukti Transfer</label>
                <input type="file" wire:model="paymentProof" accept="image/*" class="w-full text-xs rounded-xl border-slate-200 p-2 border border-slate-300">
                @error('paymentProof') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            @if ($paymentProof)
                <div class="mt-2 text-xs text-slate-500">
                    Preview Foto: <br>
                    <img src="{{ $paymentProof->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-xl mt-2 border border-slate-200 shadow-sm">
                </div>
            @endif

            <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-colors shadow-md shadow-amber-500/20">
                <span wire:loading.remove wire:target="uploadProof">Simpan Bukti Transfer</span>
                <span wire:loading wire:target="uploadProof">Mengunggah...</span>
            </button>
        </form>

        @if(session()->has('success_proof'))
            <div class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                {{ session('success_proof') }}
            </div>
        @endif
    </div>
    @elseif($order->payment_proof)
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-200/80 mb-6 flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <h3 class="font-black text-slate-900 text-sm uppercase tracking-wider mb-1">Bukti Transfer Berhasil Diunggah</h3>
            <p class="text-xs text-slate-500 mb-2">Bukti transfer Anda telah tersimpan di sistem dan sedang diverifikasi oleh admin resto.</p>
            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                <span>Lihat Bukti yang Diunggah</span> &rarr;
            </a>
        </div>
    </div>
    @endif

    <!-- Navigation Footer Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ route('catering.index') }}" class="w-full sm:w-auto text-center px-6 py-3 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
            &larr; Pesan Menu Lain
        </a>

        <button onclick="window.print()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-md shadow-slate-900/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak Invoice</span>
        </button>
    </div>

</div>

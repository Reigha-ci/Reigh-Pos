<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Pemesanan Katering Online - KedaiRobby.id' }}</title>
    <meta name="description" content="Layanan pesan katering online, nasi box, prasmanan, dan paket acara praktis dan lezat dari KedaiRobby.id">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 transition-all shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            @php
                $cateringStoreName = \App\Models\Setting::value('store_name', 'KedaiRobby.id');
                $cateringStoreLogo = \App\Models\Setting::value('store_logo');
            @endphp
            <a href="{{ route('catering.index') }}" class="flex items-center gap-3 group">
                @if($cateringStoreLogo)
                    <img src="{{ asset('storage/' . $cateringStoreLogo) }}" alt="{{ $cateringStoreName }}" class="w-10 h-10 rounded-xl object-cover shadow-md border border-slate-100 bg-white">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-500 flex items-center justify-center text-white shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform font-black text-lg">
                        {{ strtoupper(substr($cateringStoreName, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span class="font-extrabold text-lg tracking-tight text-slate-900 block leading-tight">{{ $cateringStoreName }}</span>
                    <span class="text-[11px] font-semibold text-amber-600 tracking-wider uppercase block">Katering & Event Online</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="#menu-section" class="hidden sm:inline-flex text-xs font-bold text-slate-600 hover:text-amber-600 transition-colors px-3 py-1.5 rounded-lg hover:bg-amber-50">
                    Pilihan Menu
                </a>
                <a href="#cara-pesan" class="hidden sm:inline-flex text-xs font-bold text-slate-600 hover:text-amber-600 transition-colors px-3 py-1.5 rounded-lg hover:bg-amber-50">
                    Cara Pesan
                </a>
                @php
                    $phone = \App\Models\Setting::value('catering_whatsapp', \App\Models\Setting::value('store_phone', '08123456789'));
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                @endphp
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Halo KedaiRobby.id, saya ingin bertanya tentang paket katering.') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold hover:bg-emerald-100 transition-colors border border-emerald-200/60 shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Hubungi WA</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-10 mt-20 border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-white font-black text-lg mb-3">{{ $cateringStoreName }}</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Penyedia layanan katering, nasi box, prasmanan, dan aneka menu kuliner lezat untuk acara keluarga, syukuran, meeting kantor, dan hajatan.
                    </p>
                    <p class="text-xs text-slate-500 font-medium">Bahan segar pilihan • Halal • Rasa Terjamin</p>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3">Ketentuan Pesanan Katering</h4>
                    <ul class="text-xs space-y-2 text-slate-400">
                        <li>• Pemesanan minimal H-1 sebelum tanggal acara</li>
                        <li>• Layanan pengantaran tepat waktu sampai ke lokasi</li>
                        <li>• Konfirmasi pesanan cepat via WhatsApp</li>
                        <li>• Tersedia pembayaran Transfer Bank, QRIS, & COD</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3">Informasi Kontak</h4>
                    <p class="text-xs text-slate-400 mb-2">
                        <strong>WhatsApp:</strong> {{ $phone }}
                    </p>
                    <p class="text-xs text-slate-400 mb-4">
                        <strong>Alamat Resto:</strong> {{ \App\Models\Setting::value('store_address', $cateringStoreName) }}
                    </p>
                </div>
            </div>
            <div class="pt-6 border-t border-slate-800 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span>&copy; {{ date('Y') }} {{ $cateringStoreName }}. Seluruh hak cipta dilindungi.</span>
                <span class="text-slate-600">Sistem Manajemen & Katering by {{ $cateringStoreName }}</span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>

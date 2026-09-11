<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Wajib ada untuk fitur stok

class Order extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'delivery_date' => 'date',
        'stock_reduced' => 'boolean',
    ];

    /**
     * Relasi ke tabel OrderItem (Rincian Pesanan)
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke tabel Table (Meja)
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Scope untuk pesanan Katering
     */
    public function scopeCatering($query)
    {
        return $query->where('order_type', 'catering');
    }

    /**
     * Scope untuk pesanan Dine-in / Meja
     */
    public function scopeDineIn($query)
    {
        return $query->where('order_type', '!=', 'catering')->orWhereNull('order_type');
    }

    /**
     * Cek apakah ini pesanan katering
     */
    public function getIsCateringAttribute(): bool
    {
        return $this->order_type === 'catering';
    }

    /**
     * --- LOGIKA PENGURANGAN STOK OTOMATIS ---
     * Fungsi ini dipanggil saat status pesanan berubah jadi 'Served' atau 'Paid'.
     */
    public function reduceStock()
    {
        if ($this->stock_reduced) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $product = $item->product;

                if ($product) {
                    // 1. Kurangi Stok Produk Jadi (Existing)
                    $product->decrement('stock', $item->quantity);

                    // 2. Kurangi Stok Bahan Baku (New)
                    // Ambil relasi ingredients dari produk
                    foreach ($product->ingredients as $ingredient) {
                        // total kebutuhan = qty pesanan x qty per resep
                        $neededQty = $item->quantity * $ingredient->pivot->quantity;
                        $ingredient->decrement('stock', $neededQty);
                    }
                }
            }

            $this->update(['stock_reduced' => true]);
        });
    }

    /**
     * Link WhatsApp Struk Digital Biasa (Dine-in / Takeaway)
     */
    public function getWhatsappUrlAttribute()
    {
        $storeName = \App\Models\Setting::value('store_name', 'KedaiRobby.id');
        $date = $this->created_at ? $this->created_at->format('d M Y H:i') : now()->format('d M Y H:i');

        $message = "*STRUK DIGITAL - {$storeName}*\n";
        $message .= "--------------------------------\n";
        $message .= "Order ID: #{$this->id}\n";
        $message .= "Tgl: {$date}\n";
        $message .= "Meja: " . ($this->table->name ?? ($this->is_catering ? 'Katering Online' : 'Takeaway')) . "\n";
        $message .= "--------------------------------\n";

        foreach ($this->items as $item) {
            $productName = $item->product->name ?? 'Menu';
            $message .= "{$item->quantity}x {$productName} (" . number_format($item->price, 0, ',', '.') . ")\n";
        }

        $message .= "--------------------------------\n";
        $message .= "Total: Rp " . number_format($this->total_price, 0, ',', '.') . "\n";
        $message .= "Status: " . strtoupper($this->payment_method) . " ({$this->status})\n";
        $message .= "--------------------------------\n";
        $message .= "Terima kasih telah berkunjung!\n";
        $message .= url('/');

        return "https://wa.me/?text=" . urlencode($message);
    }

    /**
     * Link WhatsApp Konfirmasi Pesanan Katering ke Restoran / Admin
     */
    public function getCateringWhatsappUrlAttribute()
    {
        $storeName = \App\Models\Setting::value('store_name', 'KedaiRobby.id');
        $storePhone = \App\Models\Setting::value('catering_whatsapp', \App\Models\Setting::value('store_phone', '628123456789'));
        
        // Normalisasi nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $storePhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $dateDelivery = $this->delivery_date ? $this->delivery_date->format('d F Y') : '-';
        $timeDelivery = $this->delivery_time ?? '-';

        $message = "*KONFIRMASI PESANAN KATERING - {$storeName}*\n";
        $message .= "================================\n";
        $message .= "No. Pesanan : #CAT-{$this->id}\n";
        $message .= "Nama Pemesan: {$this->customer_name}\n";
        $message .= "No. WhatsApp : {$this->customer_phone}\n";
        $message .= "Jadwal Kirim : {$dateDelivery} (Jam: {$timeDelivery})\n";
        $message .= "Alamat Kirim : {$this->delivery_address}\n";
        if (!empty($this->note)) {
            $message .= "Catatan Khusus : {$this->note}\n";
        }
        $message .= "================================\n";
        $message .= "*MENU PESANAN:*\n";

        foreach ($this->items as $item) {
            $productName = $item->product->name ?? 'Menu';
            $sub = number_format($item->price * $item->quantity, 0, ',', '.');
            $message .= "• {$item->quantity}x {$productName} = Rp {$sub}\n";
            if ($item->selectedVariants && $item->selectedVariants->isNotEmpty()) {
                foreach ($item->selectedVariants as $var) {
                    $message .= "   - {$var->variant_name}: {$var->option_name}\n";
                }
            }
        }

        $message .= "--------------------------------\n";
        $message .= "Subtotal   : Rp " . number_format($this->subtotal, 0, ',', '.') . "\n";
        if ($this->discount_amount > 0) {
            $message .= "Diskon     : -Rp " . number_format($this->discount_amount, 0, ',', '.') . "\n";
        }
        $message .= "*TOTAL AKHIR : Rp " . number_format($this->total_price, 0, ',', '.') . "*\n";
        if ($this->dp_amount > 0) {
            $dpPersen = $this->dp_percentage ?? round(($this->dp_amount / max(1, $this->total_price)) * 100);
            $sisaMetode = strtoupper($this->remaining_payment_method ?? 'COD');
            if ($sisaMetode === 'TRANSFER') $sisaMetode = 'TRANSFER BANK / QRIS';
            $message .= "*UANG MUKA / DP ({$dpPersen}%) : Rp " . number_format($this->dp_amount, 0, ',', '.') . "*\n";
            $message .= "*SISA PELUNASAN : Rp " . number_format($this->remaining_amount, 0, ',', '.') . "* (via {$sisaMetode})\n";
        }
        $message .= "Metode Bayar : " . strtoupper($this->payment_method) . "\n";
        $message .= "Status       : " . strtoupper($this->catering_status ?? $this->status) . "\n";
        $message .= "================================\n";
        $message .= "Link Invoice : " . route('catering.invoice', $this->id) . "\n\n";
        $message .= "Halo {$storeName}, saya melampirkan bukti transfer DP untuk pesanan katering di atas (foto terlampir di chat ini). Mohon diverifikasi dan disetujui. Terima kasih!";

        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
    }

    /**
     * Link WhatsApp dari Resto/Admin ke Pelanggan Katering
     */
    public function getCustomerWhatsappUrlAttribute()
    {
        $storeName = \App\Models\Setting::value('store_name', 'KedaiRobby.id');
        $customerPhone = $this->customer_phone ?? '';
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $statusText = match ($this->catering_status) {
            'confirmed' => 'Pesanan katering Anda telah DISETUJUI & DIKONFIRMASI dan masuk dalam jadwal produksi.',
            'cooking' => 'Pesanan katering Anda saat ini SEDANG DIMASAK dan disiapkan di dapur.',
            'delivering' => 'Pesanan katering Anda SEDANG DALAM PENGIRIMAN menuju lokasi Anda.',
            'completed' => 'Pesanan katering Anda telah SELESAI. Terima kasih telah memesan di ' . $storeName . '!',
            'rejected', 'cancelled' => 'Mohon maaf, pesanan katering Anda DITOLAK.' . ($this->rejection_reason ? " Alasan: {$this->rejection_reason}" : ""),
            default => 'Pesanan katering Anda telah kami terima dan sedang ditinjau.'
        };

        $message = "Halo Kak *{$this->customer_name}*,\n\n";
        $message .= "Update dari *{$storeName}* terkait Pesanan Katering #CAT-{$this->id}:\n";
        $message .= "📌 *Status:* " . strtoupper($this->catering_status ?? $this->status) . "\n";
        $message .= "{$statusText}\n\n";
        $message .= "Jadwal Kirim: " . ($this->delivery_date ? $this->delivery_date->format('d M Y') : '-') . " ({$this->delivery_time})\n";
        $message .= "Alamat: {$this->delivery_address}\n";
        $message .= "Total Pesanan: Rp " . number_format($this->total_price, 0, ',', '.') . "\n";
        if ($this->dp_amount > 0) {
            $message .= "DP Masuk: Rp " . number_format($this->dp_amount, 0, ',', '.') . "\n";
            $message .= "Sisa Tagihan: Rp " . number_format($this->remaining_amount, 0, ',', '.') . " (" . strtoupper($this->remaining_payment_method ?? 'COD') . ")\n";
        }
        $message .= "\nRincian invoice: " . route('catering.invoice', $this->id) . "\n\n";
        $message .= "Jika ada pertanyaan, silakan balas pesan ini ya Kak. Terima kasih! 🙏";

        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
    }
}

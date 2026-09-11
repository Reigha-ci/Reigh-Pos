<?php

namespace App\Livewire\Front;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Setting;

class CateringInvoice extends Component
{
    use WithFileUploads;

    public $orderId;
    public $order;
    public $storeName;
    public $storeAddress;
    public $storePhone;
    
    public $paymentProof;
    public $bankName;
    public $bankAccountNumber;
    public $bankAccountName;
    public $qrisImage;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->order = Order::withoutGlobalScope('tenant')
            ->with(['items.product', 'items.selectedVariants', 'tenant'])
            ->findOrFail($orderId);

        $this->storeName = Setting::value('store_name', $this->order->tenant->name ?? 'KedaiRobby.id');
        $this->storeAddress = Setting::value('store_address', $this->order->tenant->address ?? '');
        $this->storePhone = Setting::value('catering_whatsapp', Setting::value('store_phone', $this->order->tenant->phone ?? '08123456789'));

        $this->bankName = Setting::value('bank_name', 'Bank BCA');
        $this->bankAccountNumber = Setting::value('bank_account_number', '123 456 7890');
        $this->bankAccountName = Setting::value('bank_account_name', 'a.n. Kedai Robby');
        $this->qrisImage = Setting::value('qris_image', null);
    }

    public function render()
    {
        return view('livewire.front.catering-invoice')
            ->layout('layouts.catering', [
                'title' => 'Invoice Pesanan Katering #CAT-' . $this->order->id . ' - ' . $this->storeName,
            ]);
    }

    public function uploadProof()
    {
        $this->validate([
            'paymentProof' => 'required|image|max:2048', // Max 2MB
        ], [
            'paymentProof.required' => 'Silakan pilih file bukti transfer.',
            'paymentProof.image' => 'File harus berupa gambar.',
            'paymentProof.max' => 'Ukuran maksimal 2MB.',
        ]);

        $path = $this->paymentProof->store('proofs', 'public');
        
        $this->order->update([
            'payment_proof' => $path,
            'catering_status' => 'pending', // Set pending to re-evaluate if previously rejected
        ]);

        $this->paymentProof = null;
        session()->flash('success_proof', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }
}

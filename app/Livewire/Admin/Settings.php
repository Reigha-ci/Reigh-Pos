<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;

class Settings extends Component
{
    use \Livewire\WithFileUploads;

    public $storeName;
    public $storeAddress;
    public $storePhone;
    public $logo; // Uploaded file
    public $existingLogo; // Current logo path
    
    public $taxRate;
    public $serviceCharge;
    public $printerName;

    // Catering Settings
    public $cateringWhatsapp;
    public $cateringMinDp;

    // Rekening & QRIS
    public $bankName;
    public $bankAccountNumber;
    public $bankAccountName;
    public $qrisImage; // Uploaded file
    public $existingQrisImage; // Current QRIS image path
    
    // Midtrans
    public $midtransServerKey;
    public $midtransClientKey;
    public $midtransIsProduction;

    // Loyalty Program
    public $loyaltyEnabled;
    public $pointEarnRate;
    public $pointRedeemValue;

    protected function rules()
    {
        return [
            'storeName' => 'required|string|max:100',
            'storeAddress' => 'required|string|max:255',
            'storePhone' => 'nullable|string|max:20',
            'cateringWhatsapp' => 'nullable|string|max:20',
            'cateringMinDp' => 'required|numeric|min:10|max:100',
            'bankName' => 'nullable|string|max:50',
            'bankAccountNumber' => 'nullable|string|max:50',
            'bankAccountName' => 'nullable|string|max:100',
            'qrisImage' => 'nullable|image|max:2048', // 2MB Max
            'logo' => 'nullable|image|max:1024', // 1MB Max
            'taxRate' => 'required|numeric|min:0|max:100',
            'serviceCharge' => 'required|numeric|min:0|max:100',
            'printerName' => 'nullable|string|max:100',
            'midtransServerKey' => 'nullable|string',
            'midtransClientKey' => 'nullable|string',
            'midtransIsProduction' => 'required|boolean',
            'loyaltyEnabled' => 'required|boolean',
            'pointEarnRate' => 'required|numeric|min:1',
            'pointRedeemValue' => 'required|numeric|min:1',
        ];
    }

    public function mount()
    {
        $tenant = auth()->user()->tenant;

        // Tenant Settings
        $this->storeName = $tenant ? $tenant->name : Setting::value('store_name', 'KedaiRobby.id');
        $this->storeAddress = $tenant ? $tenant->address : Setting::value('store_address', '');
        $this->storePhone = $tenant ? $tenant->phone : Setting::value('store_phone', '');
        $this->existingLogo = $tenant ? $tenant->logo : Setting::value('store_logo', null);

        // Operational Settings (Key-Value)
        $this->taxRate = Setting::value('tax_rate', 11);
        $this->serviceCharge = Setting::value('service_charge', 5);
        $this->printerName = Setting::value('printer_name', 'Thermal_Printer');
        $this->cateringWhatsapp = Setting::value('catering_whatsapp', $this->storePhone);
        $this->cateringMinDp = (int) Setting::value('catering_min_dp', 30);

        // Rekening & QRIS
        $this->bankName = Setting::value('bank_name', 'Bank BCA');
        $this->bankAccountNumber = Setting::value('bank_account_number', '123 456 7890');
        $this->bankAccountName = Setting::value('bank_account_name', 'a.n. Kedai Robby');
        $this->existingQrisImage = Setting::value('qris_image', null);
        
        $this->midtransServerKey = Setting::value('midtrans_server_key', '');
        $this->midtransClientKey = Setting::value('midtrans_client_key', '');
        $this->midtransIsProduction = (bool) Setting::value('midtrans_is_production', 0);

        // Loyalty Settings
        $this->loyaltyEnabled = (bool) Setting::value('loyalty_enabled', 1);
        $this->pointEarnRate = Setting::value('point_earn_rate', 10000);
        $this->pointRedeemValue = Setting::value('point_redeem_value', 100);
    }

    public function updateSettings()
    {
        $this->validate();
        
        $tenant = auth()->user()->tenant;

        // Handle Logo Upload
        $logoPath = $this->existingLogo;
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
        }

        // Handle QRIS Upload
        $qrisPath = $this->existingQrisImage;
        if ($this->qrisImage) {
            $qrisPath = $this->qrisImage->store('qris', 'public');
        }

        // Update Tenant Info
        if ($tenant) {
            $tenant->update([
                'name' => $this->storeName,
                'address' => $this->storeAddress,
                'phone' => $this->storePhone,
                'logo' => $logoPath,
            ]);
        }

        // Update Operational Settings
        if ($logoPath) {
            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $logoPath]);
        }
        if ($qrisPath) {
            Setting::updateOrCreate(['key' => 'qris_image'], ['value' => $qrisPath]);
        }
        Setting::updateOrCreate(['key' => 'store_name'], ['value' => $this->storeName]);
        Setting::updateOrCreate(['key' => 'store_address'], ['value' => $this->storeAddress]);
        Setting::updateOrCreate(['key' => 'store_phone'], ['value' => $this->storePhone]);
        Setting::updateOrCreate(['key' => 'catering_whatsapp'], ['value' => $this->cateringWhatsapp]);
        Setting::updateOrCreate(['key' => 'catering_min_dp'], ['value' => $this->cateringMinDp]);

        Setting::updateOrCreate(['key' => 'bank_name'], ['value' => $this->bankName]);
        Setting::updateOrCreate(['key' => 'bank_account_number'], ['value' => $this->bankAccountNumber]);
        Setting::updateOrCreate(['key' => 'bank_account_name'], ['value' => $this->bankAccountName]);

        Setting::updateOrCreate(['key' => 'tax_rate'], ['value' => $this->taxRate]);
        Setting::updateOrCreate(['key' => 'service_charge'], ['value' => $this->serviceCharge]);
        Setting::updateOrCreate(['key' => 'printer_name'], ['value' => $this->printerName]);
        
        Setting::updateOrCreate(['key' => 'midtrans_server_key'], ['value' => $this->midtransServerKey]);
        Setting::updateOrCreate(['key' => 'midtrans_client_key'], ['value' => $this->midtransClientKey]);
        Setting::updateOrCreate(['key' => 'midtrans_is_production'], ['value' => $this->midtransIsProduction]);

        Setting::updateOrCreate(['key' => 'loyalty_enabled'], ['value' => $this->loyaltyEnabled]);
        Setting::updateOrCreate(['key' => 'point_earn_rate'], ['value' => $this->pointEarnRate]);
        Setting::updateOrCreate(['key' => 'point_redeem_value'], ['value' => $this->pointRedeemValue]);

        session()->flash('message', 'Pengaturan branding & toko berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.admin.settings')
            ->layout('components.admin-layout', ['header' => 'Pengaturan Toko']);
    }
}

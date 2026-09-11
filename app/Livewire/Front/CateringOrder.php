<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemVariant;
use App\Models\Tenant;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\PointTransaction;
use App\Models\Promo;
use App\Events\OrderCreated;
use App\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CateringOrder extends Component
{
    // Tenant & Store Info
    public $tenant;
    public $storeName = 'KedaiRobby.id';
    public $storeAddress = '';
    public $storePhone = '';

    // Filter & Search
    public $activeCategory = 'all';
    public $searchQuery = '';

    // Cart: [ uuid => ['product_id', 'name', 'price', 'qty', 'variants' => []] ]
    public $cart = [];

    // Modal Varian
    public $isVariantModalOpen = false;
    public $selectedProduct = null;
    public $selectedVariants = [];
    public $currentPrice = 0;
    public $variantQty = 10; // Default catering quantity

    // Form Pemesanan Katering
    public $customerName = '';
    public $customerPhone = '';
    public $deliveryDate = '';
    public $deliveryTime = '11:00';
    public $deliveryAddress = '';
    public $orderNote = '';
    public $paymentMethod = 'transfer'; // transfer, cashier, midtrans
    
    // DP Katering
    public $dpPercentage = 30; // 30, 50, 100 (Full)
    public $cateringMinDp = 30;
    public $remainingPaymentMethod = 'transfer'; // transfer, cod

    // Loyalty Program
    public $memberPhone = '';
    public $memberPoints = 0;
    public $isMember = false;
    public $pointsToRedeem = 0;
    public $pointRedeemValue = 0;
    public $pointEarnRate = 10000;
    public $loyaltyEnabled = true;

    // Promo
    public $discountAmount = 0;
    public $appliedPromoName = null;

    // UI Checkout Drawer / Modal
    public $isCheckoutOpen = false;

    public function mount($slug = null)
    {
        // Temukan tenant
        if ($slug) {
            $this->tenant = Tenant::where('slug', $slug)->first();
        }

        if (!$this->tenant) {
            $this->tenant = Tenant::first();
        }

        if ($this->tenant) {
            session()->put('tenant_id', $this->tenant->id);
            $this->storeName = Setting::value('store_name', $this->tenant->name ?? 'KedaiRobby.id');
            $this->storeAddress = Setting::value('store_address', $this->tenant->address ?? '');
            $this->storePhone = Setting::value('catering_whatsapp', Setting::value('store_phone', $this->tenant->phone ?? ''));
        } else {
            $this->storeName = Setting::value('store_name', 'KedaiRobby.id');
            $this->storeAddress = Setting::value('store_address', 'Jl. Utama');
            $this->storePhone = Setting::value('store_phone', '08123456789');
        }

        // Default tanggal acara minimal hari ini / besok
        $this->deliveryDate = now()->addDay()->format('Y-m-d');

        // Setup DP Katering
        $this->cateringMinDp = (int) Setting::value('catering_min_dp', 30);
        $this->dpPercentage = $this->cateringMinDp;

        // Setup loyalty
        $this->loyaltyEnabled = (bool) Setting::value('loyalty_enabled', 1);
        $this->pointEarnRate = (int) Setting::value('point_earn_rate', 10000);
        $this->pointRedeemValue = (int) Setting::value('point_redeem_value', 100);
    }

    public function setCategory($id)
    {
        $this->activeCategory = $id;
    }

    // --- MEMBER & LOYALTY ---
    public function updatedCustomerPhone()
    {
        if (strlen($this->customerPhone) >= 10 && empty($this->memberPhone)) {
            $this->memberPhone = $this->customerPhone;
            $this->checkMember();
        }
    }

    public function updatedMemberPhone()
    {
        $this->checkMember();
    }

    public function checkMember()
    {
        if (strlen($this->memberPhone) >= 10) {
            $customer = Customer::where('phone_number', $this->memberPhone)->first();
            if ($customer) {
                $this->isMember = true;
                $this->memberPoints = $customer->points_balance;
                if (empty($this->customerName)) {
                    $this->customerName = $customer->name;
                }
            } else {
                $this->isMember = false;
                $this->memberPoints = 0;
                $this->pointsToRedeem = 0;
            }
        } else {
            $this->isMember = false;
            $this->memberPoints = 0;
            $this->pointsToRedeem = 0;
        }
    }

    public function updatedPointsToRedeem()
    {
        if ($this->pointsToRedeem > $this->memberPoints) {
            $this->pointsToRedeem = $this->memberPoints;
        }

        if ($this->pointsToRedeem < 0) {
            $this->pointsToRedeem = 0;
        }

        $pointDiscount = $this->pointsToRedeem * $this->pointRedeemValue;
        if ($pointDiscount > $this->getSubtotal()) {
            $this->pointsToRedeem = floor($this->getSubtotal() / max(1, $this->pointRedeemValue));
        }
    }

    // --- CART LOGIC ---
    public function addToCart($productId, $qty = 1)
    {
        $product = Product::with(['variants.options'])->find($productId);
        if (!$product || !$product->is_available) return;

        if ($product->variants && $product->variants->isNotEmpty()) {
            $this->openVariantModal($product);
        } else {
            $this->directAddToCart($product, $qty);
        }
    }

    public function openVariantModal($product)
    {
        $this->selectedProduct = $product;
        $this->selectedVariants = [];
        $this->currentPrice = $product->price;
        $this->variantQty = 10;
        $this->isVariantModalOpen = true;
    }

    public function closeVariantModal()
    {
        $this->isVariantModalOpen = false;
        $this->selectedProduct = null;
        $this->selectedVariants = [];
    }

    public function updatedSelectedVariants()
    {
        $this->calculateVariantPrice();
    }

    public function calculateVariantPrice()
    {
        if (!$this->selectedProduct) return;

        $basePrice = $this->selectedProduct->price;
        $addonPrice = 0;

        foreach ($this->selectedProduct->variants as $variant) {
            if (isset($this->selectedVariants[$variant->id])) {
                $selection = $this->selectedVariants[$variant->id];

                if ($variant->type == 'radio') {
                    $option = $variant->options->find($selection);
                    if ($option) $addonPrice += $option->price;
                } elseif ($variant->type == 'checkbox' && is_array($selection)) {
                    foreach ($selection as $optId => $isSelected) {
                        if ($isSelected) {
                            $option = $variant->options->find($optId);
                            if ($option) $addonPrice += $option->price;
                        }
                    }
                }
            }
        }

        $this->currentPrice = $basePrice + $addonPrice;
    }

    public function saveVariantToCart()
    {
        if (!$this->selectedProduct) return;

        foreach ($this->selectedProduct->variants as $variant) {
            if ($variant->is_required) {
                if (!isset($this->selectedVariants[$variant->id]) || empty($this->selectedVariants[$variant->id])) {
                    $this->addError('variant_error', "Wajib memilih " . $variant->name);
                    return;
                }
                if ($variant->type == 'checkbox') {
                    $hasSelection = collect($this->selectedVariants[$variant->id])->contains(fn($val) => $val == true);
                    if (!$hasSelection) {
                        $this->addError('variant_error', "Wajib memilih " . $variant->name);
                        return;
                    }
                }
            }
        }

        $savedVariants = [];
        foreach ($this->selectedProduct->variants as $variant) {
            if (isset($this->selectedVariants[$variant->id])) {
                $selection = $this->selectedVariants[$variant->id];
                if ($variant->type == 'radio') {
                    $option = $variant->options->find($selection);
                    if ($option) {
                        $savedVariants[] = [
                            'variant_name' => $variant->name,
                            'option_name' => $option->name,
                            'option_id' => $option->id,
                            'price' => $option->price
                        ];
                    }
                } elseif ($variant->type == 'checkbox') {
                    foreach ($selection as $optId => $isSelected) {
                        if ($isSelected) {
                            $option = $variant->options->find($optId);
                            if ($option) {
                                $savedVariants[] = [
                                    'variant_name' => $variant->name,
                                    'option_name' => $option->name,
                                    'option_id' => $option->id,
                                    'price' => $option->price
                                ];
                            }
                        }
                    }
                }
            }
        }

        $uuid = (string) Str::uuid();
        $this->cart[$uuid] = [
            'product_id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'price' => $this->currentPrice,
            'variants' => $savedVariants,
            'qty' => max(1, (int) $this->variantQty),
        ];

        $this->closeVariantModal();
    }

    public function directAddToCart($product, $qty = 1)
    {
        $existingKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] == $product->id && empty($item['variants'])) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey) {
            $this->cart[$existingKey]['qty'] += $qty;
        } else {
            $uuid = (string) Str::uuid();
            $this->cart[$uuid] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'variants' => [],
                'qty' => $qty,
            ];
        }
    }

    public function incrementQty($uuid, $amount = 1)
    {
        if (isset($this->cart[$uuid])) {
            $this->cart[$uuid]['qty'] += $amount;
        }
    }

    public function decrementQty($uuid, $amount = 1)
    {
        if (isset($this->cart[$uuid])) {
            if ($this->cart[$uuid]['qty'] > $amount) {
                $this->cart[$uuid]['qty'] -= $amount;
            } else {
                unset($this->cart[$uuid]);
            }
        }
    }

    public function setItemQty($uuid, $qty)
    {
        $qty = (int) $qty;
        if (isset($this->cart[$uuid])) {
            if ($qty > 0) {
                $this->cart[$uuid]['qty'] = $qty;
            } else {
                unset($this->cart[$uuid]);
            }
        }
    }

    public function removeFromCart($uuid)
    {
        unset($this->cart[$uuid]);
    }

    public function getTotalItems()
    {
        return collect($this->cart)->sum('qty');
    }

    public function getSubtotal()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function getDiscountAmount()
    {
        $subtotal = $this->getSubtotal();
        $bestDiscount = 0;
        $bestPromoName = null;

        $promos = Promo::where('is_active', true)
            ->where('min_purchase', '<=', $subtotal)
            ->get();

        foreach ($promos as $promo) {
            $currentDiscount = ($promo->type == 'percentage') ? $subtotal * ($promo->value / 100) : $promo->value;
            if ($currentDiscount > $bestDiscount) {
                $bestDiscount = $currentDiscount;
                $bestPromoName = $promo->name;
            }
        }

        $this->discountAmount = $bestDiscount;
        $this->appliedPromoName = $bestPromoName;
        return $bestDiscount;
    }

    public function getPointDiscount()
    {
        return $this->pointsToRedeem * $this->pointRedeemValue;
    }

    public function getGrandTotal()
    {
        $total = $this->getSubtotal() - $this->getDiscountAmount() - $this->getPointDiscount();
        return max(0, $total);
    }

    public function openCheckout()
    {
        if (!empty($this->cart)) {
            $this->isCheckoutOpen = true;
        }
    }

    public function closeCheckout()
    {
        $this->isCheckoutOpen = false;
    }

    // --- PROSES SUBMIT PESANAN KATERING ---
    public function submitOrder()
    {
        $this->validate([
            'customerName' => 'required|string|min:3|max:100',
            'customerPhone' => 'required|string|min:9|max:20',
            'deliveryDate' => 'required|date|after_or_equal:today',
            'deliveryTime' => 'required|string|max:20',
            'deliveryAddress' => 'required|string|min:10|max:500',
            'orderNote' => 'nullable|string|max:500',
            'paymentMethod' => 'required|in:transfer,cashier,midtrans',
            'cart' => 'required|array|min:1',
            'dpPercentage' => 'required|in:30,50,100',
            'remainingPaymentMethod' => 'nullable|in:transfer,cod',
        ], [
            'customerName.required' => 'Nama pemesan wajib diisi.',
            'customerPhone.required' => 'Nomor WhatsApp wajib diisi agar resto dapat konfirmasi.',
            'deliveryDate.required' => 'Tanggal acara/pengiriman wajib dipilih.',
            'deliveryDate.after_or_equal' => 'Tanggal pengiriman tidak boleh sebelum hari ini.',
            'deliveryTime.required' => 'Waktu/jam pengantaran wajib diisi.',
            'deliveryAddress.required' => 'Alamat pengantaran katering wajib diisi lengkap.',
            'deliveryAddress.min' => 'Alamat pengantaran minimal 10 karakter.',
        ]);

        try {
            $orderId = null;
            $snapToken = null;

            DB::transaction(function () use (&$orderId, &$snapToken) {
                // 1. Catat Pelanggan / Member
                $customerId = null;
                if ($this->customerPhone) {
                    $customer = Customer::firstOrCreate(
                        ['phone_number' => $this->customerPhone],
                        ['name' => $this->customerName]
                    );
                    $customer->update(['last_visit' => now(), 'name' => $this->customerName]);
                    $customerId = $customer->id;
                }

                // 2. Buat Order Katering
                $order = Order::create([
                    'tenant_id' => $this->tenant->id ?? null,
                    'order_type' => 'catering',
                    'table_id' => null, // Katering tidak memakai nomor meja dine-in
                    'customer_name' => $this->customerName,
                    'customer_phone' => $this->customerPhone,
                    'delivery_date' => $this->deliveryDate,
                    'delivery_time' => $this->deliveryTime,
                    'delivery_address' => $this->deliveryAddress,
                    'note' => $this->orderNote . ($this->dpPercentage < 100 ? " (Sisa Bayar: " . strtoupper($this->remainingPaymentMethod) . ")" : ""),
                    'subtotal' => $this->getSubtotal(),
                    'discount_amount' => $this->getDiscountAmount() + $this->getPointDiscount(),
                    'promo_name' => $this->appliedPromoName . ($this->pointsToRedeem > 0 ? " + Poin" : ""),
                    'service_charge' => 0,
                    'tax_amount' => 0,
                    'total_price' => $this->getGrandTotal(),
                    'dp_percentage' => (int) $this->dpPercentage,
                    'dp_amount' => round($this->getGrandTotal() * ($this->dpPercentage / 100)),
                    'remaining_amount' => round($this->getGrandTotal() - ($this->getGrandTotal() * ($this->dpPercentage / 100))),
                    'remaining_payment_method' => $this->remainingPaymentMethod,
                    'status' => 'pending',
                    'catering_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'stock_reduced' => false,
                ]);

                $orderId = $order->id;

                // 3. Buat Item & Varian
                foreach ($this->cart as $uuid => $item) {
                    $orderItem = OrderItem::create([
                        'tenant_id' => $this->tenant->id ?? null,
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                    ]);

                    if (!empty($item['variants'])) {
                        foreach ($item['variants'] as $variant) {
                            OrderItemVariant::create([
                                'order_item_id' => $orderItem->id,
                                'product_variant_option_id' => $variant['option_id'] ?? null,
                                'variant_name' => $variant['variant_name'],
                                'option_name' => $variant['option_name'],
                                'price' => $variant['price'],
                            ]);
                        }
                    }
                }

                // 4. Potong & Berikan Poin jika ada
                if ($customerId) {
                    if ($this->pointsToRedeem > 0) {
                        PointTransaction::create([
                            'customer_id' => $customerId,
                            'order_id' => $order->id,
                            'type' => 'redeem',
                            'points' => $this->pointsToRedeem,
                            'description' => 'Redeem poin pesanan katering #CAT-' . $order->id,
                        ]);
                        Customer::where('id', $customerId)->decrement('points_balance', $this->pointsToRedeem);
                    }

                    $pointsEarned = floor($order->total_price / max(1, $this->pointEarnRate));
                    if ($pointsEarned > 0) {
                        PointTransaction::create([
                            'customer_id' => $customerId,
                            'order_id' => $order->id,
                            'type' => 'earn',
                            'points' => $pointsEarned,
                            'description' => 'Reward katering #CAT-' . $order->id,
                        ]);
                        Customer::where('id', $customerId)->increment('points_balance', $pointsEarned);
                    }
                }

                // 5. Midtrans integration jika dipilih
                if ($this->paymentMethod == 'midtrans') {
                    $midtrans = new MidtransService();
                    $snapToken = $midtrans->getSnapToken($order);
                    $order->update(['payment_token' => $snapToken]);
                }

                // Trigger event pesanan baru (dapur & admin)
                OrderCreated::dispatch($order);
            });

            // Redirect ke invoice pesanan katering
            return redirect()->route('catering.invoice', ['orderId' => $orderId]);

        } catch (\Exception $e) {
            $this->addError('submit_error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::query()
            ->with(['variants.options'])
            ->where('is_available', true);

        if ($this->activeCategory !== 'all') {
            $query->where('category_id', $this->activeCategory);
        }

        if (!empty($this->searchQuery)) {
            $query->where('name', 'like', '%' . $this->searchQuery . '%');
        }

        $products = $query->orderBy('name')->get();

        return view('livewire.front.catering-order', [
            'categories' => $categories,
            'products' => $products,
        ])->layout('layouts.catering', [
            'title' => 'Pemesanan Katering Online - ' . $this->storeName,
        ]);
    }
}

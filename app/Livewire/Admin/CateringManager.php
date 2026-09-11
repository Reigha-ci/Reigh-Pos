<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;

class CateringManager extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $activeTab = 'all'; // all, pending, confirmed, cooking, delivering, completed, cancelled
    public $dateFilter = 'all_time'; // all_time, today, tomorrow, custom
    public $customDate = '';

    // Detail Modal
    public $selectedOrder = null;
    public $isDetailModalOpen = false;

    // Reject Modal
    public $isRejectModalOpen = false;
    public $rejectOrderId = null;
    public $rejectionReason = '';

    // Manual Create Order Modal
    public $isManualModalOpen = false;
    public $manualName = '';
    public $manualPhone = '';
    public $manualDate = '';
    public $manualTime = '11:00';
    public $manualAddress = '';
    public $manualNote = '';
    public $manualPaymentMethod = 'transfer';
    public $manualItems = []; // [ ['product_id' => '', 'quantity' => 10, 'price' => 0] ]

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->manualDate = now()->addDay()->format('Y-m-d');
        $this->addItemRow();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setDateFilter($filter)
    {
        $this->dateFilter = $filter;
        $this->resetPage();
    }

    public function openDetail($orderId)
    {
        $this->selectedOrder = Order::with(['items.product', 'items.selectedVariants'])->find($orderId);
        if ($this->selectedOrder) {
            $this->isDetailModalOpen = true;
        }
    }

    public function closeDetail()
    {
        $this->isDetailModalOpen = false;
        $this->selectedOrder = null;
    }

    public function approveOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'catering_status' => 'confirmed',
                'rejection_reason' => null,
            ]);

            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->selectedOrder = $order->fresh(['items.product', 'items.selectedVariants']);
            }

            session()->flash('success', "Pesanan #CAT-{$order->id} berhasil DISETUJUI & DIKONFIRMASI!");
        }
    }

    public function openRejectModal($orderId)
    {
        $this->rejectOrderId = $orderId;
        $this->rejectionReason = '';
        $this->isRejectModalOpen = true;
    }

    public function closeRejectModal()
    {
        $this->isRejectModalOpen = false;
        $this->rejectOrderId = null;
        $this->rejectionReason = '';
    }

    public function rejectOrder()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:255',
        ], [
            'rejectionReason.required' => 'Alasan penolakan wajib diisi.',
            'rejectionReason.min' => 'Alasan penolakan minimal 3 karakter.',
        ]);

        $order = Order::find($this->rejectOrderId);
        if ($order) {
            $order->update([
                'catering_status' => 'rejected',
                'status' => 'cancelled',
                'rejection_reason' => $this->rejectionReason,
            ]);

            if ($this->selectedOrder && $this->selectedOrder->id == $order->id) {
                $this->selectedOrder = $order->fresh(['items.product', 'items.selectedVariants']);
            }

            $this->closeRejectModal();
            session()->flash('success', "Pesanan #CAT-{$order->id} telah DITOLAK.");
        }
    }

    public function markDpPaid($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            // Jika belum confirmed, otomatis confirmed saat DP diverifikasi
            $order->update([
                'catering_status' => $order->catering_status === 'pending' ? 'confirmed' : $order->catering_status,
            ]);

            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->selectedOrder = $order->fresh(['items.product', 'items.selectedVariants']);
            }

            session()->flash('success', "Pembayaran DP untuk pesanan #CAT-{$order->id} telah DIVERIFIKASI.");
        }
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'catering_status' => $status,
                'status' => in_array($status, ['cooking', 'delivering', 'completed']) ? $status : $order->status,
            ]);

            // Jika statusnya completed atau cooking, kita kurangi stok jika belum berkurang
            if (in_array($status, ['cooking', 'completed'])) {
                $order->reduceStock();
            }

            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->selectedOrder = $order->fresh(['items.product', 'items.selectedVariants']);
            }

            session()->flash('success', "Status pesanan #CAT-{$order->id} berhasil diubah ke " . strtoupper($status) . "!");
        }
    }

    public function markAsPaid($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'status' => 'paid',
                'remaining_amount' => 0,
            ]);
            $order->reduceStock();

            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->selectedOrder = $order->fresh(['items.product', 'items.selectedVariants']);
            }

            session()->flash('success', "Pesanan #CAT-{$order->id} ditandai LUNAS.");
        }
    }

    // --- MANUAL ORDER CREATION (KASIR / ADMIN INPUT) ---
    public function openManualModal()
    {
        $this->resetManualForm();
        $this->isManualModalOpen = true;
    }

    public function closeManualModal()
    {
        $this->isManualModalOpen = false;
    }

    public function addItemRow()
    {
        $this->manualItems[] = [
            'product_id' => '',
            'quantity' => 10,
            'price' => 0,
        ];
    }

    public function removeItemRow($index)
    {
        unset($this->manualItems[$index]);
        $this->manualItems = array_values($this->manualItems);
        if (empty($this->manualItems)) {
            $this->addItemRow();
        }
    }

    public function onProductSelected($index, $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->manualItems[$index]['product_id'] = $product->id;
            $this->manualItems[$index]['price'] = $product->price;
        }
    }

    public function getManualTotal()
    {
        return collect($this->manualItems)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        });
    }

    public function submitManualOrder()
    {
        $this->validate([
            'manualName' => 'required|string|min:3',
            'manualPhone' => 'required|string|min:9',
            'manualDate' => 'required|date',
            'manualTime' => 'required|string',
            'manualAddress' => 'required|string|min:5',
            'manualItems.*.product_id' => 'required|exists:products,id',
            'manualItems.*.quantity' => 'required|integer|min:1',
        ], [
            'manualName.required' => 'Nama pemesan wajib diisi.',
            'manualPhone.required' => 'Nomor telepon/WA wajib diisi.',
            'manualDate.required' => 'Tanggal pengiriman wajib diisi.',
            'manualAddress.required' => 'Alamat pengantaran wajib diisi.',
            'manualItems.*.product_id.required' => 'Pilih produk untuk setiap baris pesanan.',
        ]);

        $subtotal = $this->getManualTotal();

        $order = Order::create([
            'order_type' => 'catering',
            'table_id' => null,
            'customer_name' => $this->manualName,
            'customer_phone' => $this->manualPhone,
            'delivery_date' => $this->manualDate,
            'delivery_time' => $this->manualTime,
            'delivery_address' => $this->manualAddress,
            'note' => $this->manualNote,
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'service_charge' => 0,
            'tax_amount' => 0,
            'total_price' => $subtotal,
            'status' => 'pending',
            'catering_status' => 'confirmed', // Jika diinput admin, langsung confirmed
            'payment_method' => $this->manualPaymentMethod,
            'stock_reduced' => false,
        ]);

        foreach ($this->manualItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $this->closeManualModal();
        session()->flash('success', "Pesanan Katering #CAT-{$order->id} berhasil diinput!");
    }

    public function resetManualForm()
    {
        $this->manualName = '';
        $this->manualPhone = '';
        $this->manualDate = now()->addDay()->format('Y-m-d');
        $this->manualTime = '11:00';
        $this->manualAddress = '';
        $this->manualNote = '';
        $this->manualPaymentMethod = 'transfer';
        $this->manualItems = [];
        $this->addItemRow();
    }

    public function render()
    {
        $query = Order::query()
            ->catering()
            ->with(['items.product', 'items.selectedVariants']);

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                    ->orWhere('delivery_address', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        // Tab Status Filter
        if ($this->activeTab !== 'all') {
            $query->where('catering_status', $this->activeTab);
        }

        // Date Filter (berdasarkan delivery_date / tanggal acara)
        if ($this->dateFilter === 'today') {
            $query->whereDate('delivery_date', Carbon::today());
        } elseif ($this->dateFilter === 'tomorrow') {
            $query->whereDate('delivery_date', Carbon::tomorrow());
        } elseif ($this->dateFilter === 'custom' && !empty($this->customDate)) {
            $query->whereDate('delivery_date', $this->customDate);
        }

        // Count for badges
        $pendingCount = Order::catering()->where('catering_status', 'pending')->count();
        $confirmedCount = Order::catering()->where('catering_status', 'confirmed')->count();
        $cookingCount = Order::catering()->where('catering_status', 'cooking')->count();
        $deliveringCount = Order::catering()->where('catering_status', 'delivering')->count();

        $orders = $query->orderBy('delivery_date', 'asc')->orderBy('delivery_time', 'asc')->paginate(12);

        $availableProducts = Product::where('is_available', true)->orderBy('name')->get();

        return view('livewire.admin.catering-manager', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'cookingCount' => $cookingCount,
            'deliveringCount' => $deliveringCount,
            'availableProducts' => $availableProducts,
        ])->layout('components.admin-layout', ['header' => 'Pesanan Online & Katering']);
    }
}

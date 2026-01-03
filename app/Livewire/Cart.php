<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Services\NotaPrinter;

class Cart extends Component
{
    public $harga = [];
    public $cartItems = [];
    public $nomorSeri = [];
    public $qty = [];

    public $nama_pembeli, $no_hp, $alamat;

    public $titipan = 0;
    public $sisa = 0;
    public $total = 0;

    protected $listeners = [
        'add-to-cart' => 'addFromOutside',
    ];

    /* =========================
        HITUNG TITIPAN
    ========================== */
    public function updatedTitipan()
    {
        $titipan = intval(str_replace(['Rp', '.', ' '], '', $this->titipan));

        if ($titipan < 0) $titipan = 0;
        if ($titipan > $this->total) $titipan = $this->total;

        $this->titipan = $titipan;
        $this->sisa = $this->total - $titipan;
    }

    /* =========================
        HITUNG TOTAL
    ========================== */
    private function calculateTotal()
    {
        $this->total = collect($this->cartItems)->sum(function ($item) {
            $harga = $this->harga[$item->id] ?? 0;
            $qty   = $this->qty[$item->id] ?? $item->quantity;

            return intval($harga) * intval($qty);
        });

        $this->sisa = max(0, $this->total - intval($this->titipan));
    }

    /* =========================
        LOAD CART
    ========================== */
    public function loadCart()
    {
        $this->cartItems = CartItem::with('item')->get();

        foreach ($this->cartItems as $cartItem) {

            // Harga default
            if (!isset($this->harga[$cartItem->id])) {
                $this->harga[$cartItem->id] =
                    $cartItem->harga_manual
                    ?? $cartItem->item->harga_jual;
            }

            // Qty default
            if (!isset($this->qty[$cartItem->id])) {
                $this->qty[$cartItem->id] = $cartItem->quantity;
            }
        }

        $this->calculateTotal();
    }

    public function mount()
    {
        $this->loadCart();
    }

    /* =========================
        ADD ITEM
    ========================== */
    public function addFromOutside($payload)
    {
        $this->add($payload['itemId'], $payload['qty']);
        return redirect()->route('cart.index');
    }

    public function add($itemId, $quantity)
    {
        $item = Item::findOrFail($itemId);

        if ($item->stok < $quantity) {
            session()->flash('error', 'Stok tidak mencukupi.');
            return;
        }

        $cartItem = CartItem::where('item_id', $item->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'item_id' => $item->id,
                'quantity' => $quantity,
            ]);
        }

        $this->loadCart();
        session()->flash('success', 'Item berhasil ditambahkan.');
    }

    /* =========================
        REMOVE ITEM
    ========================== */
    public function remove($cartItemId)
    {
        $cartItem = CartItem::with('item')->findOrFail($cartItemId);

        $cartItem->item->increment('stok', $cartItem->quantity);
        $cartItem->delete();

        $this->loadCart();
        session()->flash('success', 'Barang berhasil dihapus.');
    }

    /* =========================
        UPDATE HARGA (OPTIONAL)
    ========================== */
    public function updateHarga($id)
    {
        $cartItem = CartItem::with('item')->findOrFail($id);

        $harga = intval(str_replace(['Rp', '.', ' '], '', $this->harga[$id]));

        if ($harga < $cartItem->item->harga_beli) {
            session()->flash('error', 'Harga jual tidak boleh di bawah harga kulak.');
            return;
        }

        $cartItem->update([
            'harga_manual' => $harga
        ]);

        $this->loadCart();
        session()->flash('success', 'Harga disimpan.');
    }

    /* =========================
        UPDATE QTY
    ========================== */
    public function updateQty($id)
    {
        $cartItem = CartItem::with('item')->findOrFail($id);
        $qty = max(1, intval($this->qty[$id]));

        $available = $cartItem->item->stok + $cartItem->quantity;

        if ($qty > $available) {
            session()->flash('error', 'Qty melebihi stok.');
            $this->qty[$id] = $cartItem->quantity;
            return;
        }

        $diff = $qty - $cartItem->quantity;

        if ($diff > 0) {
            $cartItem->item->decrement('stok', $diff);
        } elseif ($diff < 0) {
            $cartItem->item->increment('stok', abs($diff));
        }

        $cartItem->update(['quantity' => $qty]);

        $this->loadCart();
    }

    /* =========================
        CHECKOUT (FIXED)
    ========================== */
    private function processCheckout(string $printer)
    {
        $this->validate([
            'nama_pembeli' => 'required',
            'no_hp'        => 'required',
            'alamat'       => 'required',
        ]);

        $transactions = [];
        $total = 0;

        foreach ($this->cartItems as $cartItem) {

            $harga = intval($this->harga[$cartItem->id] ?? 0);

            if ($harga <= 0) {
                session()->flash('error', 'Semua item harus diisi harga.');
                return;
            }

            if ($harga < $cartItem->item->harga_beli) {
                session()->flash('error', 'Harga jual tidak boleh di bawah harga kulak.');
                return;
            }

            $cartItem->update([
                'harga_manual' => $harga
            ]);

            $trx = Transaction::create([
                'item_id'           => $cartItem->item_id,
                'nama_pembeli'      => $this->nama_pembeli,
                'no_hp'             => $this->no_hp,
                'alamat'            => $this->alamat,
                'jumlah'            => $cartItem->quantity,
                'harga_satuan'      => $harga,
                'total_harga'       => $harga * $cartItem->quantity,
                'nomor_seri'        => $this->nomorSeri[$cartItem->id] ?? null,
                'tanggal'           => now()->toDateString(),
                'titipan'           => $this->titipan,
                'sisa_pembayaran'   => $this->sisa,
                'status_pembayaran' => $this->sisa > 0 ? 'DP' : 'LUNAS',
            ]);

            $transactions[] = $trx;
            $total += $trx->total_harga;
        }

        CartItem::truncate();

        app(NotaPrinter::class)->print($printer, $transactions, [
            'tanggal' => now()->format('d M Y'),
            'nama_pembeli' => $this->nama_pembeli,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'total' => $total,
            'titipan' => $this->titipan,
            'sisa' => $this->sisa,
            'status' => $this->sisa > 0 ? 'DP' : 'LUNAS',
        ]);

        session()->flash('success', 'Checkout berhasil.');
        $this->dispatch('transaction-created');
    }

    public function checkout(string $printer)
    {
        $this->processCheckout($printer);
    }

    public function render()
    {
        return view('livewire.cart');
    }
}

<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Item;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
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

    public function updatedTitipan()
    {
        $titipan = intval(str_replace(['Rp', '.', ' '], '', $this->titipan));

        if ($titipan < 0) {
            $titipan = 0;
        }

        if ($titipan > $this->total) {
            $titipan = $this->total;
        }

        $this->titipan = $titipan;
        $this->sisa = $this->total - $titipan;
    }

    private function calculateTotal()
    {
        $this->total = collect($this->cartItems)->sum(function ($item) {
            $harga = $this->harga[$item->id] ?? 0;
            $qty   = $this->qty[$item->id] ?? $item->quantity;

            return intval($harga) * intval($qty);
        });

        $this->sisa = max(0, $this->total - intval($this->titipan));
    }


    public function addFromOutside($payload)
    {
        $this->add($payload['itemId'], $payload['qty']);

        return redirect()->route('cart.index');
    }

    public function loadCart()
    {
        $this->cartItems = CartItem::with('item')->get();

        foreach ($this->cartItems as $cartItem) {

            if (!isset($this->harga[$cartItem->id])) {
                $this->harga[$cartItem->id] =
                    $cartItem->harga_manual
                    ?? $cartItem->item->harga_jual;
            }

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
        session()->flash('success', 'Item berhasil ditambahkan ke keranjang.');
    }

    public function remove($cartItemId)
    {
        $cartItem = CartItem::with('item')->findOrFail($cartItemId);

        $cartItem->item->increment('stok', $cartItem->quantity);

        $cartItem->delete();

        $this->loadCart();
        session()->flash('success', 'Barang berhasil dihapus.');
    }

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
        $this->calculateTotal();

        session()->flash('success', 'Harga berhasil diperbarui.');
    }

    public function updateQty($id)
    {
        $cartItem = CartItem::with('item')->findOrFail($id);
        $qty = intval($this->qty[$id]);

        if ($qty < 1) {
            $qty = 1;
        }

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

        $cartItem->update([
            'quantity' => $qty
        ]);

        $this->loadCart();
        $this->calculateTotal();
    }

    private function processCheckout(string $printer)
    {
        $this->validate([
            'nama_pembeli' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->harga_manual === null) {
                session()->flash('error', 'Semua item harus diisi harga.');
                return;
            }
        }

        $transactions = [];
        $total = 0;

        foreach ($this->cartItems as $cartItem) {
            $item = $cartItem->item;

            if ($cartItem->harga_manual < $item->harga_beli) {
                session()->flash('error', 'Harga jual tidak boleh di bawah harga kulak.');
                return;
            }

            $trx = Transaction::create([
                'item_id' => $item->id,
                'nama_pembeli' => $this->nama_pembeli,
                'no_hp' => $this->no_hp,
                'alamat' => $this->alamat,
                'jumlah' => $cartItem->quantity,
                'harga_satuan' => $cartItem->harga_manual,
                'total_harga' => $cartItem->harga_manual * $cartItem->quantity,
                'nomor_seri' => $this->nomorSeri[$cartItem->id] ?? null,
                'tanggal' => now()->toDateString(),
                'titipan' => $this->titipan,
                'sisa_pembayaran' => $this->sisa,
                'status_pembayaran' => $this->sisa > 0 ? 'DP' : 'LUNAS',
            ]);

            $transactions[] = $trx;
            $total += $trx->total_harga;
        }

        CartItem::truncate();

        app(NotaPrinter::class)->print(
            $printer,
            $transactions,
            [
                'tanggal'      => now()->format('d M Y'),
                'nama_pembeli' => $this->nama_pembeli,
                'no_hp'        => $this->no_hp,
                'alamat'       => $this->alamat,
                'total'        => $total,
                'titipan'      => $this->titipan,
                'sisa'         => $this->sisa,
                'status'       => $this->sisa > 0 ? 'DP' : 'LUNAS',
            ]
        );

        $this->dispatch('transaction-created');
        session()->flash('success', 'Checkout berhasil.');
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

<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class Checkout extends Component
{

    public Item $item;

    public $jumlah_beli;
    public $total_harga;
    public $tanggal;

    public function mount($id)
    {
        $this->item = Item::findOrFail($id);
        $this->tanggal = now()->format('Y-m-d');
        $this->jumlah_beli = 1;
        $this->total_harga = $this->item->harga_jual;
    }

    public function processCheckout()
    {
        $this->total_harga = str_replace(['Rp', '.', ' '], '', $this->total_harga);

        $this->validate([
            'jumlah_beli' => 'required|integer|min:1',
            'total_harga' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
        ]);

        if ($this->jumlah_beli > $this->item->stok) {
            $this->addError('jumlah_beli', 'Jumlah beli melebihi stok tersedia');
            return;
        }

        if ($this->total_harga <= $this->item->harga_beli) {
            $this->addError('total_harga', 'Harga dibawah harga kulak');
            return;
        }

        DB::transaction(function () {

            $hargaSatuan = $this->total_harga;

            Transaction::create([
                'item_id'      => $this->item->id,
                'jumlah'       => $this->jumlah_beli,
                'harga_satuan' => $hargaSatuan,
                'total_harga'  => $hargaSatuan * $this->jumlah_beli,
                'harga_kulak'  => $this->item->harga_beli,
                'tanggal'      => $this->tanggal,
            ]);

            $this->item->stok -= $this->jumlah_beli;
            $this->item->save();
        });

        $this->dispatch('transaction-created');

        session()->flash('success', 'Transaksi berhasil disimpan');

        return redirect()->route('items.index');
    }

    public function incrementJumlah()
    {
        if ($this->jumlah_beli < $this->item->stok) {
            $this->jumlah_beli++;
        }
    }

    public function decrementJumlah()
    {
        if ($this->jumlah_beli > 1) {
            $this->jumlah_beli--;
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}

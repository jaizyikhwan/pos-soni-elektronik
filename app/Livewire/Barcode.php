<?php

namespace App\Livewire;

use App\Models\Item;
use Livewire\Component;

class Barcode extends Component
{
    public $barcode;
    public $item = null;
    public $errorMessage = null;
    
    public function search()
    {
        $this->reset(['item', 'errorMessage']);

        // Validasi jika barcode kosong
        if (!$this->barcode) {
            $this->errorMessage = 'Barcode tidak diberikan';
            return;
        }

        // Cari barang berdasarkan barcode
        $this->item = Item::where('barcode', trim($this->barcode))->first();

        if (!$this->item) {
            $this->errorMessage = 'Barang tidak ditemukan';
        }
    }

    public function render()
    {
        return view('livewire.barcode');
    }
}

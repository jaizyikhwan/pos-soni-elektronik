<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ItemEdit extends Component
{
    public Item $item;

    public $nama_barang;
    public $tipe_barang;
    public $barcode;
    public $harga_beli;
    public $harga_jual;
    public $stok;
    public $tanggal_order;

    public function mount(Item $item)
    {
        $this->item = $item;

        $this->nama_barang = $item->nama_barang;
        $this->tipe_barang = $item->tipe_barang;
        $this->barcode = $item->barcode;
        $this->harga_beli = "Rp " . number_format($item->harga_beli, 0, ',', '.');
        $this->harga_jual = "Rp " . number_format($item->harga_jual, 0, ',', '.');
        $this->stok = $item->stok;
        $this->tanggal_order = $item->tanggal_order;
    }

    protected function rules(): array
    {
        return [
            'nama_barang'   => 'required|string|max:255',
            'tipe_barang'   => 'required|string|max:255',

            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'barcode')
                    ->ignore($this->item->id)
                    ->whereNull('deleted_at'),
            ],

            'harga_beli'    => 'required|integer|min:1',
            'harga_jual'    => 'required|integer|gt:harga_beli',
            'stok'          => 'required|integer|min:1',
            'tanggal_order' => 'required|date',
        ];
    }

    protected array $messages = [
        'required'        => ':attribute wajib diisi.',
        'integer'         => ':attribute harus berupa angka.',
        'harga_jual.gt'   => 'Harga jual harus lebih besar dari harga kulak.',
        'stok.min'        => 'Stok harus diisi minimal 1.',
    ];

    protected array $validationAttributes = [
        'nama_barang' => 'Nama barang',
        'tipe_barang' => 'Tipe barang',
        'barcode'     => 'Barcode',
        'harga_beli'  => 'Harga kulak',
        'harga_jual'  => 'Harga jual',
        'stok'        => 'Stok',
        'tanggal_order' => 'Tanggal kulak',
    ];

    public function update()
    {
        // Bersihkan Rp dari harga beli & harga jual
        $this->harga_beli = preg_replace('/[^\d]/', '', $this->harga_beli);
        $this->harga_jual = preg_replace('/[^\d]/', '', $this->harga_jual);

        $validated = $this->validate();

        $this->item->update($validated);

        session()->flash('success', 'Data barang berhasil diperbarui.');
        return redirect()->route('items.index');
    }

    public function incrementStok()
    {
        $this->stok = ((int) $this->stok) + 1;
    }

    public function decrementStok()
    {
        $this->stok = max(0, ((int) $this->stok) - 1);
    }

    public function render()
    {
        return view('livewire.items.edit');
    }
}

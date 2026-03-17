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
    public $stok;
    public $tanggal_order;

    public function mount(Item $item)
    {
        $this->item = $item;

        $this->nama_barang = $item->nama_barang;
        $this->tipe_barang = $item->tipe_barang;
        $this->barcode = $item->barcode;
        $this->harga_beli = "Rp " . number_format($item->harga_beli, 0, ',', '.');
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
            'stok'          => 'required|integer|min:1',
            'tanggal_order' => 'required|date',
        ];
    }

    protected array $messages = [
        'required'        => ':attribute wajib diisi.',
        'integer'         => ':attribute harus berupa angka.',
        'stok.min'        => 'Stok harus diisi minimal 1.',
    ];

    protected array $validationAttributes = [
        'nama_barang' => 'Nama barang',
        'tipe_barang' => 'Tipe barang',
        'barcode'     => 'Barcode',
        'harga_beli'  => 'Harga kulak',
        'stok'        => 'Stok',
        'tanggal_order' => 'Tanggal kulak',
    ];

    public function update()
    {
        // Bersihkan Rp dari harga beli & harga jual
        $this->harga_beli = preg_replace('/[^\d]/', '', $this->harga_beli);

        $validated = $this->validate();

        $this->item->update([
            'nama_barang' => $validated['nama_barang'],
            'tipe_barang' => $validated['tipe_barang'],
            'barcode' => $validated['barcode'] ?? null,
            'harga_beli' => $validated['harga_beli'],
            'stok' => $validated['stok'],
            'tanggal_order' => $validated['tanggal_order'],
        ]);

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

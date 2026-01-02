<?php

namespace App\Livewire\Items;

use Livewire\Component;
use App\Models\Item;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemCreate extends Component
{
    public string $nama_barang = '';
    public string $tipe_barang = '';
    public ?string $barcode = null;
    public $harga_beli = null;
    public $harga_jual = null;
    public ?int $stok = null;
    public ?string $tanggal_order = null;

    protected function rules(): array
    {
        return [
            'nama_barang'   => 'required|string|max:255',
            'tipe_barang'   => 'required|string|max:255',

            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'barcode')->whereNull('deleted_at'),
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
        'harga_beli'  => 'Harga kulak',
        'harga_jual'  => 'Harga jual',
        'stok'        => 'Stok',
    ];

    public function store()
    {
        // Bersihkan format rupiah
        $this->harga_beli = preg_replace('/[^\d]/', '', $this->harga_beli);
        $this->harga_jual = $this->harga_jual ? preg_replace('/[^\d]/', '', $this->harga_jual) : null;

        $validated = $this->validate();

        $payload = [
            'nama_barang' => $validated['nama_barang'],
            'tipe_barang' => $validated['tipe_barang'],
            'barcode' => $validated['barcode'] ?? null,
            'harga_beli' => $validated['harga_beli'],
            'harga_jual' => $validated['harga_jual'],
            'stok' => $validated['stok'],
            'tanggal_order' => $validated['tanggal_order'] ?? now(),
        ];

        DB::beginTransaction();

        try {
            if (!empty($payload['barcode'])) {
                $existing = Item::withTrashed()->where('barcode', $payload['barcode'])->first();
            } else {
                $existing = Item::withTrashed()
                    ->where('nama_barang', $payload['nama_barang'])
                    ->where('tipe_barang', $payload['tipe_barang'])
                    ->first();
            }

            // CASE A: Soft deleted
            if ($existing && $existing->trashed()) {
                $existing->restore();
                $existing->fill($payload);
                $existing->save();

                DB::commit();
                session()->flash('success', 'Barang lama berhasil diaktifkan kembali.');
                return redirect()->route('items.index');
            }

            // CASE B: Aktif
            if ($existing && !$existing->trashed()) {
                $stok_lama = (int)$existing->stok;
                $stok_baru = (int)$payload['stok'];

                $total_stok = $stok_lama + $stok_baru;

                if ($stok_lama > 0) {
                    $total_nilai_lama = $stok_lama * $existing->harga_beli;
                    $total_nilai_baru = $stok_baru * $payload['harga_beli'];

                    $harga_beli_rata2 = round(($total_nilai_lama + $total_nilai_baru) / max(1, $total_stok));

                    $existing->harga_beli = $harga_beli_rata2;
                    $existing->harga_jual = round($harga_beli_rata2 * 1.2);
                } else {
                    $existing->harga_beli = $payload['harga_beli'];
                    $existing->harga_jual = round($payload['harga_beli'] * 1.2);
                }

                $existing->stok = $total_stok;

                if (!empty($payload['barcode']) && empty($existing->barcode)) {
                    $existing->barcode = $payload['barcode'];
                }

                $existing->save();

                DB::commit();
                session()->flash('success', 'Stok barang berhasil ditambahkan.');
                return redirect()->route('items.index');
            }

            // CASE C: Barang baru
            Item::create($payload);

            DB::commit();
            session()->flash('success', 'Barang baru berhasil ditambahkan.');
            return redirect()->route('items.index');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Livewire ItemCreate error', ['error' => $e->getMessage()]);

            session()->flash('error', 'Terjadi kesalahan saat menyimpan barang.');
        }
    }

    public function incrementStok()
    {
        $this->stok = ((int)$this->stok) + 1;
    }

    public function decrementStok()
    {
        $this->stok = max(0, ((int)$this->stok) - 1);
    }


    public function render()
    {
        return view('livewire.items.create');
    }
}

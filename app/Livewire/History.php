<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;
use App\Services\NotaPrinter;

class History extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $month;
    public $search = '';

    public $selectedTransaction;
    public $showModal = false;

    protected $queryString = [
        'month'  => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMonth()
    {
        $this->resetPage();
    }

    public function showDetail($id)
    {
        $this->selectedTransaction = Transaction::with('item')->findOrFail($id);
        $this->showModal = true;
    }

    public function printUlang(string $printer)
    {
        if (!$this->selectedTransaction) return;

        if ($this->selectedTransaction->status === 'CANCELLED') {
            session()->flash('error', 'Transaksi dibatalkan, nota tidak bisa dicetak.');
            return;
        }

        // Dispatch event ke frontend
        $this->dispatch('print-receipt', [
            'printer'      => $printer,
            'pembeli'      => $this->selectedTransaction->nama_pembeli ?? 'N/A',
            'no_hp'        => $this->selectedTransaction->no_hp ?? '',
            'alamat'       => $this->selectedTransaction->alamat ?? '',
            'items'        => [[
                'nama_barang'   => $this->selectedTransaction->item->nama_barang ?? 'N/A',
                'quantity'      => (int) $this->selectedTransaction->jumlah,
                'harga_satuan'  => (int) $this->selectedTransaction->harga_satuan,
                'total'         => (int) $this->selectedTransaction->total_harga,
                'nomor_seri'    => $this->selectedTransaction->nomor_seri ?? '',
            ]],
            'total'        => (int) $this->selectedTransaction->total_harga,
            'titipan'      => (int) ($this->selectedTransaction->titipan ?? 0),
            'sisa'         => (int) ($this->selectedTransaction->sisa_pembayaran ?? 0),
            'status'       => $this->selectedTransaction->status_pembayaran ?? 'LUNAS',
            'tanggal'      => $this->selectedTransaction->tanggal?->format('d M Y') ?? now()->format('d M Y'),
        ]);

        session()->flash('success', 'Mengirim ke printer...');
    }

    public function cancelTransaction($id)
    {
        Transaction::where('id', $id)->update([
            'status' => 'CANCELLED'
        ]);

        $this->showModal = false;
    }

    public function render()
    {
        $historyTransaction = Transaction::with('item')
            ->when($this->month, function ($q) {
                $q->whereMonth('tanggal', $this->month);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('nama_pembeli', 'like', '%' . $this->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $this->search . '%')
                        ->orWhereHas('item', function ($qi) {
                            $qi->where(function ($qii) {
                                $qii->where('nama_barang', 'like', '%' . $this->search . '%')
                                    ->orWhere('tipe_barang', 'like', '%' . $this->search . '%');
                            });
                        });
                });
            })
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('livewire.history', [
            'historyTransaction' => $historyTransaction
        ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Item;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $labels = [];
    public $data = [];

    public $totalPendapatan;
    public $totalBarang;
    public $totalKeuntungan;
    public $totalHargaBarang;

    public $todayTransactionCount = 0;
    public $todayRevenue = 0;
    public $todayProfit = 0;

    protected $listeners = [
        'transaction-created' => 'loadTodaySales',
    ];

    public function mount()
    {
        $today = Carbon::today();
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;

        $today = Carbon::today();

        /**
         * ===============================
         * PENJUALAN HARI INI
         * ===============================
         */
        $todayTransactions = Transaction::with('item')
            ->where('status', 'VALID')
            ->whereDate('tanggal', $today)
            ->get();

        $this->todayTransactionCount = $todayTransactions->count();

        $this->todayRevenue = $todayTransactions->sum('total_harga');

        $profitToday = 0;

        foreach ($todayTransactions as $trx) {
            if ($trx->item) {
                $profitToday += $trx->total_harga
                    - ($trx->item->harga_beli * $trx->jumlah);
            }
        }

        $this->todayProfit = $profitToday;

        $transaksi = Transaction::with('item')
            ->where('status', 'VALID')
            ->whereYear('tanggal', $tahunIni)
            ->whereMonth('tanggal', $bulanIni)
            ->get();

        $mingguan = [
            'Minggu 1' => 0,
            'Minggu 2' => 0,
            'Minggu 3' => 0,
            'Minggu 4' => 0,
            'Minggu 5' => 0,
        ];

        foreach ($transaksi as $trx) {
            if (!$trx->item) continue;

            $tanggal = Carbon::parse($trx->tanggal);
            $mingguKe = ceil($tanggal->day / 7);
            $label = 'Minggu ' . $mingguKe;

            $keuntungan = $trx->total_harga - ($trx->item->harga_beli * $trx->jumlah);

            if (isset($mingguan[$label])) {
                $mingguan[$label] += $keuntungan;
            }
        }

        $this->labels = array_keys($mingguan);
        $this->data = array_values($mingguan);

        $this->totalPendapatan = Transaction::where('status', 'VALID')
            ->whereYear('tanggal', $tahunIni)
            ->whereMonth('tanggal', $bulanIni)
            ->sum('total_harga');
        $this->totalBarang = Item::sum('stok');
        $this->totalHargaBarang = Item::sum(DB::raw('harga_beli * stok'));

        $allTransactions = Transaction::with('item')
            ->where('status', 'VALID')
            ->whereYear('tanggal', $tahunIni)
            ->whereMonth('tanggal', $bulanIni)
            ->get();
        $keuntungan = 0;

        foreach ($allTransactions as $trx) {
            if ($trx->item) {
                $keuntungan += $trx->total_harga - ($trx->item->harga_beli * $trx->jumlah);
            }
        }

        $this->totalKeuntungan = $keuntungan;
        $this->loadTodaySales();
    }

    public function loadTodaySales()
    {
        $today = Carbon::today();

        $todayTransactions = Transaction::with('item')
            ->where('status', 'VALID')
            ->whereDate('tanggal', $today)
            ->get();

        $this->todayTransactionCount = $todayTransactions->count();
        $this->todayRevenue = $todayTransactions->sum('total_harga');

        $profit = 0;
        foreach ($todayTransactions as $trx) {
            if ($trx->item) {
                $profit += $trx->total_harga
                    - ($trx->item->harga_beli * $trx->jumlah);
            }
        }

        $this->todayProfit = $profit;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}

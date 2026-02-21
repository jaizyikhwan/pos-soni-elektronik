<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
            Riwayat Transaksi
        </h2>
    </div>

    <div class="flex flex-col md:flex-row gap-3">
        {{-- SEARCH --}}
        <input type="text" wire:model.live="search" placeholder="Cari pembeli / barang / no HP..."
            class="px-4 py-2 rounded-lg border text-sm w-full md:w-72 rounded-xl border border-zinc-300 dark:border-zinc-700 
                   bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100
                   focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none" />

        {{-- FILTER BULAN --}}
        <select wire:model.live="month"
            class="px-3 py-2 border text-sm rounded-xl border border-zinc-300 dark:border-zinc-700 
                   bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100
                   focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none">
            <option value="">Semua Bulan</option>
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}">
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
    </div>

    {{-- ================= MOBILE VIEW (CARD) ================= --}}
    <div class="md:hidden space-y-3">
        @forelse ($historyTransaction as $transaction)
            <div wire:click="showDetail({{ $transaction->id }})"
                class="border rounded-xl p-4 bg-white dark:bg-zinc-900
                       hover:bg-zinc-50 dark:hover:bg-zinc-800
                       transition cursor-pointer">

                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-zinc-800 dark:text-zinc-100">
                            {{ $transaction->item->nama_barang ?? '-' }}
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ $transaction->item->tipe_barang ?? '-' }}
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ $transaction->nama_pembeli ?? '-' }}
                        </div>
                    </div>

                    <span
                        class="text-xs px-2 py-1 rounded
                        {{ $transaction->status === 'CANCELLED' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                        {{ $transaction->status }}
                    </span>
                </div>

                <div class="mt-3 flex justify-between text-sm">
                    <div>
                        Qty: <strong>{{ $transaction->jumlah }}</strong>
                    </div>
                    <div class="text-zinc-500">
                        {{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d M Y') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-sm text-zinc-500 py-8">
                Tidak ada transaksi.
            </div>
        @endforelse
    </div>

    {{-- ================= DESKTOP VIEW (TABLE) ================= --}}
    <div
        class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border
                border-zinc-200 dark:border-zinc-800 overflow-hidden">

        <table class="min-w-full text-sm text-left text-zinc-700 dark:text-zinc-300">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-xs uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama Pembeli</th>
                    <th class="px-6 py-3">Nama Barang</th>
                    <th class="px-6 py-3">Tipe Barang</th>
                    <th class="px-6 py-3">Qty</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($historyTransaction as $index => $transaction)
                    <tr wire:click="showDetail({{ $transaction->id }})"
                        class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer">

                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $transaction->nama_pembeli ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $transaction->item->nama_barang ?? '-' }}</td>
                        <td class="px-6 py-4">
                            {{ $transaction->item->tipe_barang ?? '-' }}
                        </td>
                        <td class="px-6 py-4">{{ $transaction->jumlah }}</td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="text-xs px-2 py-1 rounded
                                {{ $transaction->status === 'CANCELLED' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ $transaction->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-zinc-500">
                            Tidak ada transaksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= MODAL DETAIL TRANSAKSI ================= --}}
    @if ($showModal && $selectedTransaction)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-lg p-6 space-y-4">

                <h3 class="text-lg font-semibold">
                    Detail Transaksi
                </h3>

                {{-- INFO PEMBELI --}}
                <div class="text-sm space-y-1">
                    <div>Nama: {{ $selectedTransaction->nama_pembeli }}</div>
                    <div>No HP: {{ $selectedTransaction->no_hp }}</div>
                    <div>Alamat: {{ $selectedTransaction->alamat }}</div>
                    <div>
                        Tanggal:
                        {{ \Carbon\Carbon::parse($selectedTransaction->tanggal)->translatedFormat('d F Y') }}
                    </div>
                    <div>
                        Status:
                        <strong
                            class="{{ $selectedTransaction->status === 'CANCELLED' ? 'text-red-600' : 'text-green-600' }}">
                            {{ $selectedTransaction->status }}
                        </strong>
                    </div>
                </div>

                <hr>

                {{-- INFO BARANG --}}
                <div class="text-sm space-y-1">
                    <div>Barang: {{ $selectedTransaction->item->nama_barang ?? '-' }}</div>
                    <div>Tipe: {{ $selectedTransaction->item->tipe_barang ?? '-' }}</div>
                    <div>No Seri: {{ $selectedTransaction->nomor_seri ?? '-' }}</div>
                    <div>Qty: {{ $selectedTransaction->jumlah }}</div>
                    <div>
                        Total:
                        Rp {{ number_format($selectedTransaction->total_harga, 0, ',', '.') }}
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="flex flex-wrap justify-end gap-2 pt-4">
                    <button wire:click="$set('showModal', false)" class="px-3 py-2 text-sm border rounded">
                        Tutup
                    </button>

                    <button wire:click="printUlang('thermal')" data-printer="thermal"
                        class="px-3 py-2 text-sm bg-blue-600 text-white rounded">
                        Print Thermal
                    </button>

                    <button wire:click="printUlang('dotmatrix')" data-printer="dotmatrix"
                        class="px-3 py-2 text-sm bg-indigo-600 text-white rounded">
                        Print Dotmatrix
                    </button>

                    @if ($selectedTransaction->status === 'VALID')
                        <button wire:click="confirmCancel({{ $selectedTransaction->id }})"
                            class="px-3 py-2 text-sm bg-red-600 text-white rounded">
                            Batalkan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- konfirmasi pembatalan --}}
    @if ($confirmingCancel)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-sm p-6 space-y-4">
                <h3 class="text-lg font-semibold">Konfirmasi</h3>
                <p>Anda yakin ingin membatalkan transaksi ini?</p>
                <div class="flex justify-end gap-2 pt-4">
                    <button wire:click="$set('confirmingCancel', false)" class="px-3 py-2 text-sm border rounded">
                        Batal
                    </button>

                    <button wire:click="cancelConfirmed" class="px-3 py-2 text-sm bg-red-600 text-white rounded">
                        Ya, batalkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6">
        {{ $historyTransaction->links() }}
    </div>

</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('print-receipt', async (event) => {
            const payload = event[0];
            console.log("📋 Print event received:", payload);

            const printBtn = document.querySelector('[data-printer="' + payload.printer + '"]');

            try {
                if (printBtn) {
                    printBtn.disabled = true;
                    printBtn.textContent = 'Memproses...';
                }

                try {
                    if (typeof window.printWithQZTray !== 'function') {
                        throw new Error('Printer module belum tersedia');
                    }

                    const result = await window.printWithQZTray(payload);

                    if (result.success) {
                        console.log("✓ Print successful");
                        alert(result.message);
                    } else {
                        console.error("✗ Print failed:", result.message);
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('❌ Print execution error:', error);
                    alert(error.message || 'Error mencetak');
                }
            } catch (error) {
                console.error('❌ Print execution error:', error);
                alert(`Error: ${error.message}`);
            } finally {
                if (printBtn) {
                    printBtn.disabled = false;
                    printBtn.textContent = 'Print ' +
                        (payload.printer === 'thermal' ? 'Thermal' : 'Dotmatrix');
                }
            }
        });
    });
</script>

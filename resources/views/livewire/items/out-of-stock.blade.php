<div class="max-w-7xl mx-auto px-4 py-6 space-y-6 md:pb-0 pb-48">

    <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
        Barang Habis
    </h2>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="px-4 py-3 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= TABLE WRAPPER ================= --}}
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800
               rounded-2xl shadow-sm overflow-hidden">

        {{-- ================= MOBILE TABLE ================= --}}
        <div class="md:hidden overflow-x-auto">
            <table class="min-w-full text-sm text-left text-zinc-700 dark:text-zinc-300">

                <thead class="bg-zinc-100 dark:bg-zinc-800 text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">Barang</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                            {{-- NAMA + INFO --}}
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $item->nama_barang }}
                                </div>
                                <div class="text-xs text-zinc-500">
                                    {{ $item->tipe_barang }}
                                </div>
                                <div class="text-xs font-medium mt-1">
                                    Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-0.5 rounded-md bg-red-100 text-red-600
                                           dark:bg-red-900 dark:text-red-300 text-xs font-semibold">
                                    Habis
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3 text-center">
                                <button wire:click="restoreItem({{ $item->id }})" wire:loading.attr="disabled"
                                    class="px-2 py-1 text-xs rounded-lg
                                           bg-[var(--color-accent)]
                                           text-[var(--color-accent-foreground)]
                                           hover:opacity-90 transition">
                                    Restore
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-zinc-500">
                                Tidak ada barang habis stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm text-left text-zinc-700 dark:text-zinc-300">

                <thead class="bg-zinc-100 dark:bg-zinc-800 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">Nama Barang</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">

                            <td class="px-6 py-4 font-medium">
                                {{ $item->nama_barang }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->tipe_barang }}
                            </td>

                            <td class="px-6 py-4">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full
                                           bg-red-100 text-red-600
                                           dark:bg-red-900 dark:text-red-300">
                                    Habis
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button wire:click="restoreItem({{ $item->id }})" wire:loading.attr="disabled"
                                    class="px-4 py-1.5 rounded-md
                                           bg-[var(--color-accent)]
                                           text-[var(--color-accent-foreground)]
                                           text-sm hover:opacity-90 transition">
                                    Restore
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-zinc-500">
                                Tidak ada barang habis stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $items->links() }}
    </div>

</div>

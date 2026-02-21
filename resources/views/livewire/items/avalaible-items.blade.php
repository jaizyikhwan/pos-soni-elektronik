<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
        Daftar Barang
    </h2>

    {{-- Notification --}}
    @if (session()->has('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @elseif(session()->has('error'))
        <div class="px-4 py-3 rounded-xl bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search --}}
    <div>
        <input type="text" wire:model.live="search" placeholder="Cari nama / tipe / barcode..."
            class="w-full md:w-96 px-4 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 
                   bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100
                   focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none">
    </div>

    {{-- Table Wrapper --}}
    <div
        class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 
               rounded-2xl shadow-sm overflow-hidden">

        {{-- ================= MOBILE TABLE ================= --}}
        <div class="md:hidden overflow-x-auto">
            <table class="min-w-full text-sm text-left text-zinc-700 dark:text-zinc-300">

                <thead class="bg-zinc-100 dark:bg-zinc-800 text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">Barang</th>
                        <th class="px-4 py-3 text-center">Stok</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($items as $item)
                        <tr wire:click="goToEdit({{ $item->id }})"
                            class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50">

                            {{-- NAMA + INFO --}}
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-800 dark:text-zinc-100">
                                    {{ $item->nama_barang }}
                                </div>
                                <div class="text-xs text-zinc-500">
                                    {{ $item->tipe_barang }}
                                </div>
                                <div class="text-xs font-medium mt-1">
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- STOK --}}
                            <td class="px-4 py-3 text-center">
                                <span class="w-8 font-semibold text-zinc-800 dark:text-zinc-100">
                                    {{ $item->stok }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col gap-1">
                                    <button wire:click.stop="confirmDelete({{ $item->id }})"
                                        class="px-2 py-1 text-xs rounded-lg border border-red-500 text-red-500">
                                        Hapus
                                    </button>

                                    <button wire:click.stop="addToCart({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1 rounded-lg border border-accent text-accent text-xs">
                                        + Keranjang
                                    </button>

                                    <a wire:click.stop href="{{ route('items.checkout', $item->id) }}"
                                        class="px-2 py-1 text-xs rounded-lg bg-accent text-white">
                                        Jual
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-zinc-500">
                                Barang tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- ================= DESKTOP TABLE ================= --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm text-left text-zinc-700 dark:text-zinc-300">

                <thead
                    class="bg-zinc-100 dark:bg-zinc-800 text-xs uppercase font-semibold 
                         text-zinc-600 dark:text-zinc-300">
                    <tr>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3 text-center">Stok</th>
                        <th class="px-6 py-3">Harga Kulak</th>
                        <th class="px-6 py-3">Harga Jual</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($items as $item)
                        <tr wire:click="goToEdit({{ $item->id }})"
                            class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">

                            <td class="px-6 py-4">{{ $item->nama_barang }}</td>
                            <td class="px-6 py-4">{{ $item->tipe_barang }}</td>

                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $item->stok }}
                            </td>

                            <td class="px-6 py-4">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 font-medium">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click.stop="confirmDelete({{ $item->id }})"
                                        class="px-3 py-1 rounded-lg border border-red-500 text-red-500 text-xs">
                                        Hapus
                                    </button>

                                    <button wire:click.stop="addToCart({{ $item->id }})"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1 rounded-lg border border-accent text-accent text-xs">
                                        + Keranjang
                                    </button>

                                    <a wire:click.stop href="{{ route('items.checkout', $item->id) }}"
                                        class="px-3 py-1 rounded-lg bg-accent text-white text-xs">
                                        Jual
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-zinc-500">
                                Barang tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $items->links() }}
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-sm p-6 space-y-4 animate-fade-in">

                <h3 class="text-lg font-semibold text-center text-red-600">
                    Hapus Barang?
                </h3>

                <p class="text-sm text-center text-zinc-500">
                    Yakin ingin menghapus <strong>{{ $selectedItemName }}</strong>?
                </p>

                {{-- ACTION --}}
                <div class="flex justify-between gap-2 pt-2">
                    <button wire:click="$set('confirmingDelete', false)"
                        class="w-full px-4 py-2 rounded-lg border border-zinc-300 text-sm text-zinc-700 dark:text-zinc-300">
                        Batal
                    </button>

                    <button wire:click="deleteItem" class="w-full px-4 py-2 rounded-lg bg-red-600 text-white text-sm">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showPinModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-sm p-6 space-y-4 animate-fade-in">

                <h3 class="text-lg font-semibold text-center">
                    Masukkan PIN
                </h3>

                <p class="text-sm text-center text-zinc-500">
                    Akses edit barang dilindungi
                </p>

                {{-- PIN INPUT --}}
                <input type="password" inputmode="numeric" maxlength="4" wire:model.defer="pin"
                    wire:keydown.enter="submitPin"
                    class="w-full text-center tracking-widest text-2xl px-4 py-3 rounded-xl
                       border border-zinc-300 dark:border-zinc-700
                       bg-white dark:bg-zinc-900
                       focus:ring-2 focus:ring-accent focus:border-accent
                       outline-none"
                    placeholder="• • • •" />

                {{-- ERROR --}}
                @if ($pinError)
                    <div class="text-sm text-center text-red-600">
                        {{ $pinError }}
                    </div>
                @endif

                {{-- ACTION --}}
                <div class="flex justify-between pt-2 gap-2">
                    <button wire:click="$set('showPinModal', false)" class="w-full px-4 py-2 rounded-lg border text-sm">
                        Batal
                    </button>

                    <button wire:click="submitPin" class="w-full px-4 py-2 rounded-lg bg-accent text-white text-sm">
                        Masuk
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

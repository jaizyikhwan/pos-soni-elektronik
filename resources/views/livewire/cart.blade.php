<div class="max-w-7xl mx-auto px-4 py-6 space-y-6 md:pb-0 pb-40">

    <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
        Keranjang Belanja
    </h2>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="px-4 py-3 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="px-4 py-3 rounded-xl bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- EMPTY --}}
    @if (empty($cartItems) || count($cartItems) === 0)
        <div class="text-center py-12 text-zinc-500">
            Keranjang masih kosong
        </div>
    @else
        {{-- ================= MOBILE ================= --}}
        <div class="space-y-4 md:hidden">
            @php $total = 0; @endphp

            @foreach ($cartItems as $cartItem)
                @php
                    $hargaManual = $cartItem->harga_manual ?? 0;
                    $subtotal = $hargaManual * $cartItem->quantity;
                    $total += $subtotal;
                @endphp

                <div
                    class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 space-y-3">

                    <div>
                        <div class="font-semibold text-zinc-800 dark:text-zinc-100">
                            {{ $cartItem->item->nama_barang }}
                        </div>
                        <div class="text-xs text-zinc-500">
                            {{ $cartItem->item->tipe_barang }}
                        </div>
                    </div>

                    <input type="text" wire:model.lazy="nomorSeri.{{ $cartItem->id }}"
                        placeholder="No Seri (opsional)"
                        class="w-full px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm" />

                    <div class="text-sm text-zinc-500">
                        Qty: <span class="font-medium">{{ $cartItem->quantity }}</span>
                    </div>

                    {{-- Harga --}}
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model.lazy="harga.{{ $cartItem->id }}" placeholder="Harga jual"
                            class="w-full px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm">

                        <button wire:click="updateHarga({{ $cartItem->id }})"
                            class="px-3 py-2 text-sm rounded-lg border border-accent text-accent">
                            Simpan
                        </button>
                    </div>

                    <div class="flex justify-between items-center">

                        <button wire:click="remove({{ $cartItem->id }})"
                            onclick="return confirm('Hapus barang dari keranjang?')" class="text-sm text-red-500">
                            Hapus
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= DESKTOP ================= --}}
        <div
            class="hidden md:block bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden">

            <table class="w-full text-sm">
                <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                    <tr>
                        <th class="px-6 py-3 text-left">Barang</th>
                        <th class="px-6 py-3 text-left">Tipe</th>
                        <th class="px-6 py-3 text-center">Harga Kulak</th>
                        <th class="px-6 py-3">No Seri</th>
                        <th class="px-6 py-3">Harga</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @php $total = 0; @endphp

                    @foreach ($cartItems as $cartItem)
                        @php
                            $hargaManual = $cartItem->harga_manual ?? 0;
                            $subtotal = $hargaManual * $cartItem->quantity;
                            $total += $subtotal;
                        @endphp

                        <tr>
                            <td class="px-6 py-4 font-medium">
                                {{ $cartItem->item->nama_barang }}
                            </td>

                            <td class="px-6 py-4 font-medium">
                                {{ $cartItem->item->tipe_barang }}
                            </td>

                            <td class="px-6 py-4 text-center text-zinc-500">
                                Rp {{ number_format($cartItem->item->harga_beli, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <input type="text" wire:model.lazy="nomorSeri.{{ $cartItem->id }}"
                                    placeholder="Opsional"
                                    class="w-40 px-2 py-1 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm">
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-center">
                                    <input type="text" wire:model.defer="harga.{{ $cartItem->id }}"
                                        class="w-28 px-2 py-1 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm">

                                    <button wire:click="updateHarga({{ $cartItem->id }})" class="text-accent text-sm">
                                        Simpan
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <input type="number" min="1" wire:model.lazy="qty.{{ $cartItem->id }}"
                                        class="w-16 px-2 py-1 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm">

                                    <button wire:click="updateQty({{ $cartItem->id }})" class="text-accent text-sm">
                                        Update
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button wire:click="remove({{ $cartItem->id }})"
                                    onclick="return confirm('Hapus barang dari keranjang?')"
                                    class="text-red-500 text-sm">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAYMENT SUMMARY --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 space-y-4">

            {{-- Total --}}
            <div class="flex justify-between items-center">
                <span class="text-zinc-500">Total Belanja</span>
                <span class="text-xl font-bold">
                    Rp {{ number_format($this->total, 0, ',', '.') }}
                </span>
            </div>

            {{-- Titipan --}}
            <div class="flex justify-between items-center gap-4">
                <label class="text-zinc-500">Titipan / DP</label>
                <input type="text" wire:model.lazy="titipan" placeholder="0"
                    class="w-40 text-right px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700">
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700">

            {{-- Sisa --}}
            <div class="flex justify-between items-center">
                <span class="text-zinc-700 dark:text-zinc-300 font-medium">
                    Sisa Pembayaran
                </span>

                <span class="text-2xl font-bold text-red-600">
                    Rp {{ number_format($sisa, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- CHECKOUT FORM --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input wire:model="nama_pembeli" placeholder="Nama Pembeli"
                    class="px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700">
                <input wire:model="no_hp" placeholder="No HP"
                    class="px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700">
                <input wire:model="alamat" placeholder="Alamat"
                    class="px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700">
            </div>

            <div class="flex flex-col md:flex-row gap-3">
                <button wire:click="checkout('dotmatrix')"
                    class="flex-1 py-3 rounded-xl bg-accent text-white font-semibold">
                    Cetak Dotmatrix
                </button>

                <button wire:click="checkout('thermal')"
                    class="flex-1 py-3 rounded-xl bg-zinc-700 text-white font-semibold">
                    Cetak Thermal
                </button>
            </div>
        </div>

    @endif
</div>

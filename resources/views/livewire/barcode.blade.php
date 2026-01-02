<div id="barcode-search-container" class="container mx-auto max-w-3xl px-4 py-6">

    <h2 class="text-2xl font-semibold mb-6 text-zinc-900 dark:text-zinc-100">
        Scan Barcode
    </h2>

    {{-- ERROR --}}
    @if ($errorMessage)
        <div
            class="mb-5 p-4 rounded-xl
            bg-red-100 dark:bg-red-900/40
            text-red-700 dark:text-red-200
            border border-red-300 dark:border-red-700">
            {{ $errorMessage }}
        </div>
    @endif

    {{-- SCANNER CARD --}}
    <div
        class="rounded-2xl border border-zinc-300 dark:border-zinc-700
        bg-white dark:bg-zinc-900
        shadow-sm p-5">

        {{-- HEADER --}}
        <div class="flex items-center gap-3 mb-4">
            <div
                class="flex items-center justify-center
                w-10 h-10 rounded-xl
                bg-green-100 dark:bg-green-900/40">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    Kamera
                </h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Arahkan barcode ke dalam kotak
                </p>
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="flex gap-2 mb-4">
            <button id="start-scan"
                class="px-4 py-2 rounded-lg
                bg-green-600 text-white
                hover:bg-green-700 transition">
                Mulai Scan
            </button>

            <button id="stop-scan"
                class="hidden px-4 py-2 rounded-lg
                bg-red-600 text-white
                hover:bg-red-700 transition">
                Stop
            </button>
        </div>

        {{-- CAMERA VIEW --}}
        <div id="reader"
            class="w-full max-w-md mx-auto
            rounded-xl overflow-hidden
            border border-zinc-300 dark:border-zinc-700
            bg-black">
        </div>
    </div>

    {{-- HASIL --}}
    @if ($item)
        <div
            class="mt-6 rounded-2xl border border-zinc-300 dark:border-zinc-700
            bg-white dark:bg-zinc-900
            shadow-sm p-5">

            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-3">
                Detail Barang
            </h3>

            <div class="space-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                <p><strong>Nama:</strong> {{ $item->nama_barang }}</p>
                <p><strong>Tipe:</strong> {{ $item->tipe_barang }}</p>
                <p><strong>Barcode:</strong> {{ $item->barcode }}</p>
                <p><strong>Harga Beli:</strong> Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</p>
                <p><strong>Harga Jual:</strong> Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                <p><strong>Stok:</strong> {{ $item->stok }}</p>
            </div>

            <button
                class="mt-4 w-full py-2 rounded-xl
                bg-accent text-accent-foreground
                hover:bg-accent-content transition">
                Tambah ke Keranjang
            </button>
        </div>
    @endif

</div>

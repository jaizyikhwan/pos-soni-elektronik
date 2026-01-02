<div id="barcode-edit-container" class="container p-4 md:pb-0 pb-48">

    {{-- HILANGKAN SPINNER INPUT NUMBER --}}
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <h1 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-200 mb-8">
        Edit Barang
    </h1>

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-4">

        @php
            $inputBase = "block w-full mt-1 px-3 py-2 text-sm 
                bg-white dark:bg-zinc-800
                rounded-xl shadow-sm
                text-zinc-800 dark:text-zinc-100
                focus:outline-none focus:ring-2";

            function inputClass($error)
            {
                return $error
                    ? 'border border-red-500 focus:ring-red-500'
                    : 'border border-zinc-300 dark:border-zinc-700 focus:ring-accent';
            }
        @endphp

        {{-- NAMA BARANG --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Nama Barang</span>
            <input type="text" wire:model.defer="nama_barang"
                class="{{ $inputBase }} {{ inputClass($errors->has('nama_barang')) }}"
                placeholder="Masukkan nama barang">
            @error('nama_barang')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </label>

        {{-- TIPE --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Tipe</span>
            <input type="text" wire:model.defer="tipe_barang"
                class="{{ $inputBase }} {{ inputClass($errors->has('tipe_barang')) }}"
                placeholder="Masukkan tipe barang">
            @error('tipe_barang')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </label>

        {{-- BARCODE (OPTIONAL) --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Barcode (opsional)</span>

            <div class="mt-1 space-y-2 md:space-y-0 md:flex md:gap-2">
                {{-- INPUT --}}
                <input type="text" wire:model.defer="barcode" id="barcodeInput"
                    placeholder="Scan atau masukkan barcode"
                    class="{{ $inputBase }} {{ inputClass($errors->has('barcode')) }}">

                {{-- ACTION BUTTONS --}}
                <div class="grid grid-cols-2 gap-2 md:flex md:gap-2">
                    <button type="button" id="start-scan"
                        class="w-full bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700">
                        Scan
                    </button>

                    <button type="button" id="stop-scan"
                        class="w-full hidden bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">
                        Stop
                    </button>
                </div>
            </div>

            @error('barcode')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </label>

        {{-- CAMERA --}}
        <div id="reader"
            class="mt-3 hidden w-full rounded-xl overflow-hidden border border-zinc-300 dark:border-zinc-700"></div>


        {{-- HARGA KULAK --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Harga Kulak</span>
            <input type="text" wire:model.defer="harga_beli"
                class="rupiah-input {{ $inputBase }} {{ inputClass($errors->has('harga_beli')) }}"
                placeholder="Masukkan harga kulak">
            @error('harga_beli')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </label>

        {{-- HARGA JUAL --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Harga Jual</span>
            <input type="text" wire:model.defer="harga_jual"
                class="rupiah-input {{ $inputBase }} {{ inputClass($errors->has('harga_jual')) }}"
                placeholder="Masukkan harga jual">
            @error('harga_jual')
                <span class="text-red-500 text-xs mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </span>
            @enderror
        </label>

        {{-- TANGGAL --}}
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Tanggal Kulak</span>
            <input type="datetime-local" wire:model.defer="tanggal_order"
                class="{{ $inputBase }} {{ inputClass($errors->has('tanggal_order')) }} cursor-pointer"
                onclick="this.showPicker?.()">
            @error('tanggal_order')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </label>

        {{-- STOK --}}
        <div class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Jumlah Stok</span>
            <div class="flex items-center mt-1 gap-1">
                <button type="button" wire:click="decrementStok"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    −
                </button>

                <input type="number" wire:model.lazy="stok" min="1"
                    class="w-20 text-center px-3 py-2 rounded-lg
                        {{ inputClass($errors->has('stok')) }}">

                <button type="button" wire:click="incrementStok"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    +
                </button>
            </div>
            @error('stok')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="update"
            class="bg-accent text-white px-4 py-2 rounded-xl shadow
                   hover:bg-accent-content transition
                   disabled:opacity-50 disabled:cursor-not-allowed">
            Update Barang
        </button>

    </form>
</div>

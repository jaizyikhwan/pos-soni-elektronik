<div class="container mx-auto p-6">

    
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

    <h2 class="text-2xl font-semibold mb-6 text-zinc-800 dark:text-zinc-100">
        Checkout Barang
    </h2>

    <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow border border-zinc-200 dark:border-zinc-800">

        
        <div class="mb-6 space-y-1">
            <div class="flex justify-between">
                <span class="text-zinc-500">Nama Barang</span>
                <span class="font-semibold"><?php echo e($item->nama_barang); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500">Tipe</span>
                <span class="font-semibold"><?php echo e($item->tipe_barang); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-zinc-500">Stok Tersedia</span>
                <span class="font-semibold"><?php echo e($item->stok); ?></span>
            </div>
        </div>

        
        <div class="mb-4">
            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                Harga Kulak
            </label>
            <input type="text" value="Rp <?php echo e(number_format($item->harga_beli, 0, ',', '.')); ?>" disabled
                class="w-full mt-1 px-3 py-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700">
        </div>

        
        <div class="mb-4">
            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                Harga Jual (wajib diisi)
            </label>

            <input type="text" wire:model.defer="total_harga" placeholder="Rp 0"
                class="w-full mt-1 px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700
                       bg-white dark:bg-zinc-900">

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['total_harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mb-4">
            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                Jumlah Beli
            </label>

            <div class="flex items-center mt-1">
                <button type="button" wire:click="decrementJumlah"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    −
                </button>

                <input type="number" wire:model="jumlah_beli" min="1" max="<?php echo e($item->stok); ?>"
                    class="w-20 text-center px-3 py-2 border-t border-b border-zinc-300 dark:border-zinc-700">

                <button type="button" wire:click="incrementJumlah"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    +
                </button>
            </div>

            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['jumlah_beli'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-sm text-red-600 mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="mb-6">
            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                Tanggal Transaksi
            </label>
            <input type="date" wire:model="tanggal"
                class="w-full mt-1 px-3 py-2 rounded-lg border border-zinc-300 dark:border-zinc-700">
        </div>

        
        <div class="flex gap-2">
            <a href="<?php echo e(route('items.index')); ?>"
                class="px-4 py-2 border rounded-lg border-zinc-300 dark:border-zinc-700">
                Batal
            </a>

            <button wire:click="processCheckout" wire:loading.attr="disabled"
                class="px-4 py-2 bg-accent text-white rounded-lg">
                Simpan Transaksi
            </button>
        </div>

    </div>

</div><?php /**PATH /Users/jaizyikhwan/Documents/coding/laravel/soni-elektronik/resources/views/livewire/checkout.blade.php ENDPATH**/ ?>
<div id="barcode-create-container" class="container p-4">

    
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
        Input Barang
    </h1>

    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <form wire:submit.prevent="store" class="space-y-4">

        <?php
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
        ?>

        
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Nama Barang</span>
            <input type="text" wire:model.defer="nama_barang" placeholder="Masukkan nama barang"
                class="<?php echo e($inputBase); ?> <?php echo e(inputClass($errors->has('nama_barang'))); ?>">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nama_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </label>

        
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Tipe</span>
            <input type="text" wire:model.defer="tipe_barang" placeholder="Masukkan tipe barang"
                class="<?php echo e($inputBase); ?> <?php echo e(inputClass($errors->has('tipe_barang'))); ?>">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tipe_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </label>

        
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Barcode (opsional)</span>
            <div class="flex gap-2 mt-1">
                <input type="text" wire:model.defer="barcode" id="barcodeInput"
                    placeholder="Scan atau masukkan barcode"
                    class="<?php echo e($inputBase); ?> <?php echo e(inputClass($errors->has('barcode'))); ?> flex-1">
                <button type="button" id="start-scan"
                    class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700">
                    Scan
                </button>
                <button type="button" id="stop-scan"
                    class="hidden bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">
                    Stop
                </button>
            </div>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </label>

        <div id="reader" wire:ignore class="mt-3 hidden"></div>

        
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Harga Kulak</span>
            <input type="text" wire:model.defer="harga_beli" placeholder="Masukkan harga kulak"
                class="rupiah-input <?php echo e($inputBase); ?> <?php echo e(inputClass($errors->has('harga_beli'))); ?>">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['harga_beli'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </label>

        
        <label class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Tanggal Kulak</span>
            <input type="datetime-local" wire:model.defer="tanggal_order"
                class="<?php echo e($inputBase); ?> <?php echo e(inputClass($errors->has('tanggal_order'))); ?> cursor-pointer"
                onclick="this.showPicker?.()">
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tanggal_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </label>

        
        <div class="block text-sm">
            <span class="text-zinc-700 dark:text-zinc-300">Jumlah Stok</span>
            <div class="flex items-center mt-1 gap-1">
                <button type="button" wire:click="decrementStok"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    −
                </button>

                <input type="number" wire:model.lazy="stok" min="1"
                    class="w-20 text-center px-3 py-2 rounded-lg
                        <?php echo e(inputClass($errors->has('stok'))); ?>">

                <button type="button" wire:click="incrementStok"
                    class="px-3 py-2.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg">
                    +
                </button>
            </div>
            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['stok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="store"
            class="bg-accent text-white px-4 py-2 rounded-xl shadow
           hover:bg-accent-content transition
           disabled:opacity-50 disabled:cursor-not-allowed">
            Tambah Barang
        </button>

    </form>
</div><?php /**PATH /Users/jaizyikhwan/Documents/coding/laravel/soni-elektronik/resources/views/livewire/items/create.blade.php ENDPATH**/ ?>
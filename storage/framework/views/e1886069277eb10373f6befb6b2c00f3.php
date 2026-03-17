<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 class="text-2xl font-semibold text-zinc-800 dark:text-zinc-100">
            Riwayat Transaksi
        </h2>
    </div>

    <div class="flex flex-col md:flex-row gap-3">
        
        <input type="text" wire:model.live="search" placeholder="Cari pembeli / barang / no HP..."
            class="px-4 py-2 rounded-lg border text-sm w-full md:w-72 rounded-xl border border-zinc-300 dark:border-zinc-700 
                   bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100
                   focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none" />

        
        <select wire:model.live="month"
            class="px-3 py-2 border text-sm rounded-xl border border-zinc-300 dark:border-zinc-700 
                   bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100
                   focus:ring-2 focus:ring-accent focus:border-accent focus:outline-none">
            <option value="">Semua Bulan</option>
            <!--[if BLOCK]><![endif]--><?php for($i = 1; $i <= 12; $i++): ?>
                <option value="<?php echo e($i); ?>">
                    <?php echo e(\Carbon\Carbon::create()->month($i)->translatedFormat('F')); ?>

                </option>
            <?php endfor; ?><!--[if ENDBLOCK]><![endif]-->
        </select>
    </div>

    
    <div class="md:hidden space-y-3">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $historyTransaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div wire:click="showDetail(<?php echo e($transaction->id); ?>)"
                class="border rounded-xl p-4 bg-white dark:bg-zinc-900
                       hover:bg-zinc-50 dark:hover:bg-zinc-800
                       transition cursor-pointer">

                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-zinc-800 dark:text-zinc-100">
                            <?php echo e($transaction->item->nama_barang ?? '-'); ?>

                        </div>
                        <div class="text-xs text-zinc-500">
                            <?php echo e($transaction->item->tipe_barang ?? '-'); ?>

                        </div>
                        <div class="text-xs text-zinc-500">
                            <?php echo e($transaction->nama_pembeli ?? '-'); ?>

                        </div>
                    </div>

                    <span
                        class="text-xs px-2 py-1 rounded
                        <?php echo e($transaction->status === 'CANCELLED' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'); ?>">
                        <?php echo e($transaction->status); ?>

                    </span>
                </div>

                <div class="mt-3 flex justify-between text-sm">
                    <div>
                        Qty: <strong><?php echo e($transaction->jumlah); ?></strong>
                    </div>
                    <div class="text-zinc-500">
                        <?php echo e(\Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d M Y')); ?>

                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-sm text-zinc-500 py-8">
                Tidak ada transaksi.
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    
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
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $historyTransaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:click="showDetail(<?php echo e($transaction->id); ?>)"
                        class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer">

                        <td class="px-6 py-4"><?php echo e($index + 1); ?></td>
                        <td class="px-6 py-4"><?php echo e($transaction->nama_pembeli ?? '-'); ?></td>
                        <td class="px-6 py-4"><?php echo e($transaction->item->nama_barang ?? '-'); ?></td>
                        <td class="px-6 py-4">
                            <?php echo e($transaction->item->tipe_barang ?? '-'); ?>

                        </td>
                        <td class="px-6 py-4"><?php echo e($transaction->jumlah); ?></td>
                        <td class="px-6 py-4">
                            <?php echo e(\Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y')); ?>

                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="text-xs px-2 py-1 rounded
                                <?php echo e($transaction->status === 'CANCELLED' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'); ?>">
                                <?php echo e($transaction->status); ?>

                            </span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-zinc-500">
                            Tidak ada transaksi.
                        </td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showModal && $selectedTransaction): ?>
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-900 rounded-xl w-full max-w-lg p-6 space-y-4">

                <h3 class="text-lg font-semibold">
                    Detail Transaksi
                </h3>

                
                <div class="text-sm space-y-1">
                    <div>Nama: <?php echo e($selectedTransaction->nama_pembeli); ?></div>
                    <div>No HP: <?php echo e($selectedTransaction->no_hp); ?></div>
                    <div>Alamat: <?php echo e($selectedTransaction->alamat); ?></div>
                    <div>
                        Tanggal:
                        <?php echo e(\Carbon\Carbon::parse($selectedTransaction->tanggal)->translatedFormat('d F Y')); ?>

                    </div>
                    <div>
                        Status:
                        <strong
                            class="<?php echo e($selectedTransaction->status === 'CANCELLED' ? 'text-red-600' : 'text-green-600'); ?>">
                            <?php echo e($selectedTransaction->status); ?>

                        </strong>
                    </div>
                </div>

                <hr>

                
                <div class="text-sm space-y-1">
                    <div>Barang: <?php echo e($selectedTransaction->item->nama_barang ?? '-'); ?></div>
                    <div>Tipe: <?php echo e($selectedTransaction->item->tipe_barang ?? '-'); ?></div>
                    <div>No Seri: <?php echo e($selectedTransaction->nomor_seri ?? '-'); ?></div>
                    <div>Qty: <?php echo e($selectedTransaction->jumlah); ?></div>
                    <div>
                        Total:
                        Rp <?php echo e(number_format($selectedTransaction->total_harga, 0, ',', '.')); ?>

                    </div>
                </div>

                
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

                    <!--[if BLOCK]><![endif]--><?php if($selectedTransaction->status === 'VALID'): ?>
                        <button wire:click="confirmCancel(<?php echo e($selectedTransaction->id); ?>)"
                            class="px-3 py-2 text-sm bg-red-600 text-white rounded">
                            Batalkan
                        </button>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($confirmingCancel): ?>
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="mt-6">
        <?php echo e($historyTransaction->links()); ?>

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
</script><?php /**PATH /Users/jaizyikhwan/Documents/coding/laravel/soni-elektronik/resources/views/livewire/history.blade.php ENDPATH**/ ?>
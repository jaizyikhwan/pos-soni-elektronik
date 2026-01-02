<?php

namespace App\Livewire\Actions;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStok
{
    public function handle($id, $stokBaru)
    {
        try {
            DB::transaction(function () use ($id, $stokBaru) {

                $item = Item::findOrFail($id);

                $item->update([
                    'stok' => $stokBaru
                ]);

                if ($stokBaru == 0) {
                    $item->markOutOfStock();
                } else {
                    $item->update(['stok' => $stokBaru]);
                }
            });

            return [
                'success' => true,
                'message' => 'Stok berhasil diperbarui',
            ];
        } catch (\Exception $e) {
            Log::error('Gagal update stok', [
                'item_id' => $id,
                'stok_baru' => $stokBaru,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal update stok: ' . $e->getMessage()
            ];
        }
    }
}

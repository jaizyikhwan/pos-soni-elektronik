<?php

namespace App\Livewire\Items;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class OutOfStock extends Component
{
    use WithPagination;

    public function restoreItem($id)
    {
        DB::transaction(function () use ($id) {
            $item = Item::onlyTrashed()->findOrFail($id);
            $item->restore();
            $item->update(['stok' => 1]);
        });

        session()->flash('success', 'Barang berhasil dikembalikan.');
    }

    public function render()
    {
        $items = Item::onlyTrashed()->paginate(7);

        return view('livewire.items.out-of-stock', [
            'items' => $items
        ]);
    }
}

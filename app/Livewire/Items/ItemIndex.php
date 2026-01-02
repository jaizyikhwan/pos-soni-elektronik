<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Livewire\Component;
use App\Models\CartItem;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Livewire\Actions\UpdateStok;

class ItemIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $search = '';

    public $confirmingDelete = false;
    public $selectedItemId = null;
    public $selectedItemName = null;

    public $showPinModal = false;
    public $pin = '';
    public $pinError = null;
    public $pendingEditItemId = null;

    protected $listeners = ['stokUpdated' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($itemId)
    {
        $item = Item::findOrFail($itemId);

        $this->selectedItemId = $item->id;
        $this->selectedItemName = $item->nama_barang;
        $this->confirmingDelete = true;
    }

    public function deleteItem()
    {
        $updateStok = new UpdateStok();
        $result = $updateStok->handle($this->selectedItemId, 0);

        session()->flash(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );

        $this->confirmingDelete = false;
        $this->selectedItemId = null;

        $this->dispatch('stokUpdated');
    }

    public function updateStokItem($itemId, $stokBaru)
    {
        if ($stokBaru < 0) return;

        $updateStok = new UpdateStok();
        $result = $updateStok->handle($itemId, $stokBaru);

        session()->flash(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );

        $this->dispatch('stokUpdated');
    }

    public function goToEdit($id)
    {
        $this->pendingEditItemId = $id;
        $this->pin = '';
        $this->pinError = null;
        $this->showPinModal = true;
    }

    public function submitPin()
    {
        if ($this->pin !== '1976') {
            $this->pinError = 'PIN salah';
            $this->pin = '';
            return;
        }

        $itemId = $this->pendingEditItemId;

        $this->reset([
            'showPinModal',
            'pin',
            'pinError',
            'pendingEditItemId',
        ]);

        return redirect()->route('items.edit', $itemId);
    }

    public function addToCart($itemId)
    {
        DB::transaction(function () use ($itemId) {

            $item = Item::lockForUpdate()->findOrFail($itemId);

            if ($item->stok < 1) {
                session()->flash('error', 'Stok habis.');
                return;
            }

            $cartItem = CartItem::where('item_id', $itemId)->first();

            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                CartItem::create([
                    'item_id' => $itemId,
                    'quantity' => 1,
                ]);
            }

            $item->decrement('stok', 1);
        });

        session()->flash('success', 'Item ditambahkan ke keranjang.');
        return redirect()->route('cart.index');
    }

    public function render()
    {
        $items = Item::query()
            ->where('stok', '>', 0)
            ->search($this->search)
            ->latest()
            ->paginate(7);

        return view('livewire.items.avalaible-items', [
            'items' => $items,
        ]);
    }
}

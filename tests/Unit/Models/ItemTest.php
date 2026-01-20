<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_can_be_created_with_valid_data()
    {
        $item = Item::factory()->create([
            'nama_barang' => 'Laptop',
            'tipe_barang' => 'Electronics',
            'harga_jual' => 10000000,
            'harga_beli' => 8000000,
            'stok' => 5,
        ]);

        $this->assertDatabaseHas('items', [
            'nama_barang' => 'Laptop',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_has_transactions_relationship()
    {
        $item = Item::factory()->create();
        $transaction = Transaction::factory()->create(['item_id' => $item->id]);

        $this->assertTrue($item->transactions()->where('id', $transaction->id)->exists());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_scope_finds_by_nama_barang()
    {
        Item::factory()->create(['nama_barang' => 'Laptop Dell']);
        Item::factory()->create(['nama_barang' => 'Mouse Logitech']);

        $results = Item::search('Laptop')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laptop Dell', $results->first()->nama_barang);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_scope_finds_by_tipe_barang()
    {
        Item::factory()->create(['tipe_barang' => 'Elektronik']);
        Item::factory()->create(['tipe_barang' => 'Furniture']);

        $results = Item::search('Elektronik')->get();

        $this->assertCount(1, $results);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_scope_finds_by_barcode()
    {
        Item::factory()->create(['barcode' => '123456789']);
        Item::factory()->create(['barcode' => '987654321']);

        $results = Item::search('123456789')->get();

        $this->assertCount(1, $results);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_scope_supports_multiple_keywords()
    {
        Item::factory()->create(['nama_barang' => 'Laptop Dell', 'tipe_barang' => 'Elektronik']);
        Item::factory()->create(['nama_barang' => 'Mouse Logitech', 'tipe_barang' => 'Elektronik']);

        $results = Item::search('Laptop Elektronik')->get();

        $this->assertCount(1, $results);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_returns_all_items_if_search_is_empty()
    {
        Item::factory(3)->create();

        $results = Item::search(null)->get();

        $this->assertCount(3, $results);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_cannot_be_soft_deleted_if_stok_is_not_zero()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Item tidak boleh dihapus jika stok masih ada');

        $item = Item::factory()->create(['stok' => 5]);
        $item->delete();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_can_be_soft_deleted_if_stok_is_zero()
    {
        $item = Item::factory()->create(['stok' => 0]);
        $item->delete();

        $this->assertSoftDeleted($item);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_can_be_force_deleted()
    {
        $item = Item::factory()->create(['stok' => 5]);
        $item->forceDelete();

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function mark_out_of_stock_sets_stok_to_zero_and_deletes()
    {
        $item = Item::factory()->create(['stok' => 10]);

        $item->markOutOfStock();

        $this->assertEquals(0, $item->fresh()->stok);
        $this->assertSoftDeleted($item);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function item_attributes_are_cast_correctly()
    {
        $item = Item::factory()->create([
            'harga_beli' => 1000,
            'harga_jual' => 1500,
            'stok' => 5,
            'tanggal_order' => '2026-01-19',
        ]);

        $this->assertIsInt($item->harga_beli);
        $this->assertIsInt($item->harga_jual);
        $this->assertIsInt($item->stok);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $item->tanggal_order);
    }
}

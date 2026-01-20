<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function transaction_can_be_created_with_valid_data()
    {
        $item = Item::factory()->create();
        $transaction = Transaction::factory()->create([
            'item_id' => $item->id,
            'jumlah' => 2,
            'nama_pembeli' => 'Jaizy',
            'status' => 'COMPLETED',
        ]);

        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'nama_pembeli' => 'Jaizy',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function transaction_has_item_relationship()
    {
        $item = Item::factory()->create();
        $transaction = Transaction::factory()->create(['item_id' => $item->id]);

        $this->assertTrue($transaction->item()->where('id', $item->id)->exists());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function is_cancelled_returns_true_for_cancelled_status()
    {
        $transaction = Transaction::factory()->create(['status' => 'CANCELLED']);

        $this->assertTrue($transaction->isCancelled());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function is_cancelled_returns_false_for_other_statuses()
    {
        $transaction = Transaction::factory()->create(['status' => 'COMPLETED']);

        $this->assertFalse($transaction->isCancelled());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function transaction_attributes_are_cast_correctly()
    {
        $transaction = Transaction::factory()->create();

        $this->assertIsInt($transaction->jumlah);
        $this->assertIsInt($transaction->harga_satuan);
        $this->assertIsInt($transaction->total_harga);
    }
}

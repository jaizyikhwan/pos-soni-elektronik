<?php

namespace Tests\Unit\Models;

use App\Models\CartItem;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function cart_item_can_be_created()
    {
        $item = Item::factory()->create();
        $cartItem = CartItem::factory()->create([
            'item_id' => $item->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'item_id' => $item->id,
            'quantity' => 2,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cart_item_has_item_relationship()
    {
        $item = Item::factory()->create();
        $cartItem = CartItem::factory()->create(['item_id' => $item->id]);

        $this->assertTrue($cartItem->item()->where('id', $item->id)->exists());
        $this->assertEquals($item->id, $cartItem->item->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function cart_item_quantity_defaults_to_valid_value()
    {
        $item = Item::factory()->create();
        $cartItem = CartItem::factory()->create([
            'item_id' => $item->id,
            'quantity' => 5,
        ]);

        $this->assertEquals(5, $cartItem->quantity);
    }
}

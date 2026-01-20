<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function cart_page_requires_authentication()
    {
        $this->get('/cart')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/cart')->assertSuccessful();
    }
}

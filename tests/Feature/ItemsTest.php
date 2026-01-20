<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function items_page_requires_authentication()
    {
        $this->get('/items')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_items()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/items')->assertSuccessful();
    }
}

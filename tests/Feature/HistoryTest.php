<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function history_page_requires_authentication()
    {
        $this->get('/history')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/history')->assertSuccessful();
    }
}

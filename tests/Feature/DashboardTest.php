<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function dashboard_requires_authentication()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/dashboard')->assertSuccessful();
    }
}

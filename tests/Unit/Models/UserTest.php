<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_be_created_with_valid_data()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Hash::check('password123', $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_initials_are_generated_correctly()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $this->assertEquals('JD', $user->initials());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_initials_with_single_name()
    {
        $user = User::factory()->create(['name' => 'John']);
        $this->assertEquals('J', $user->initials());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_initials_with_multiple_words()
    {
        $user = User::factory()->create(['name' => 'Muhammad Jaizy Ikhwan']);
        $this->assertEquals('MJ', $user->initials());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function password_is_hidden_in_serialization()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('two_factor_secret', $userArray);
    }
}

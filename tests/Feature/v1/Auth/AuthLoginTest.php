<?php

namespace Feature\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $username = fake()->unique()->username();
        $email = fake()->unique()->safeEmail();
        $password = 'strongPassword123!';

        $registered = User::factory()->makeUser([
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
        ]);

        $this->actingAs(User::factory()->makeGuest());

        $response = $this->postJson(route('api.v1.auth.login'), [
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $registered->id)
            ->assertJsonPath('data.personality.email', $email);

        $this->assertAuthenticatedAs($registered);
    }

    public function test_invalid_credentials_return_plain_validation_errors(): void
    {
        $username = fake()->unique()->username();

        User::factory()->create([
            'username' => $username,
            'password' => Hash::make('correct-password'),
        ]);

        $this->actingAs(User::factory()->makeGuest());

        $response = $this->postJson(route('api.v1.auth.login'), [
            'username' => $username,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['general'],
            ]);

        $this->assertIsString($response->json('errors.general'));
    }

    public function test_banned_user_cannot_login(): void
    {
        $password = 'strongPassword123!';

        // Создаём забаненного пользователя
        $user = User::factory()->makeUser([
            'password' => Hash::make($password),
            'banned_at' => now(),
        ]);

        $this->actingAs(User::factory()->makeGuest());

        $response = $this->postJson(route('api.v1.auth.login'), [
            'username' => $user->username,
            'password' => $password,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
            ]);
    }
}

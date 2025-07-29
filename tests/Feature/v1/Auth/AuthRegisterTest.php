<?php

namespace Feature\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_register(): void
    {
        $payload = [
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(route('api.v1.auth.register'), $payload);

        $response->assertUnauthorized();
    }

    public function test_authenticated_guest_can_register_and_data_persists(): void
    {
        $guest = User::factory()->makeGuest();

        $this->actingAs($guest);

        $payload = [
            'email' => fake()->unique()->safeEmail(),
            'password' => 'StrongPassword1!',
            'password_confirmation' => 'StrongPassword1!',
        ];

        $response = $this->postJson(route('api.v1.auth.register'), $payload);

        $response->assertOk()
            ->assertJsonPath('data.personality.email', $payload['email']);

        $guest->refresh();

        $this->assertSame($payload['email'], $guest->email);
        $this->assertTrue($guest->hasRole('user'));
    }

    public function test_validation_error_returns_plain_strings(): void
    {
        $duplicatedEmail = fake()->unique()->safeEmail();

        User::factory()->create(['email' => $duplicatedEmail]);

        Sanctum::actingAs(User::factory()->makeGuest());

        $payload = [
            'email' => $duplicatedEmail,
            'password' => 'StrongPassword1!',
            'password_confirmation' => 'StrongPassword1!',
        ];

        $response = $this->postJson(route('api.v1.auth.register'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The email has already been taken.',
                'errors'  => [
                    'email' => 'The email has already been taken.',
                ],
            ]);

        $this->assertIsString($response->json('errors.email'));
    }
}

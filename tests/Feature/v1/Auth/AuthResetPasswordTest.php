<?php

namespace Feature\v1\Auth;

use App\Models\User;
use Exception;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthResetPasswordTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws Exception
     */
    public function test_user_can_request_and_reset_password(): void
    {
        Notification::fake();

        $user = User::factory()->makeUser();

        $this->actingAs($user);

        // 1. Запрос на восстановление пароля
        $response = $this->postJson(route('api.v1.auth.forgot-password'), [
            'email' => $user->email,
        ]);

        $response->assertNoContent();

        $token = null;

        // 2. Проверяем, что уведомление отправлено и получаем токен
        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function ($notification) use (&$token) {
                $token = $notification->token;

                return true;
            }
        );

        $this->assertNotNull($token);

        // 3. Запрос на сброс пароля
        $newPassword = 'NewPassword1!';
        $response = $this->postJson(route('api.v1.auth.reset-password'), [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertNoContent();

        // 4. Проверяем, что пароль действительно изменился
        $user->refresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }
}

<?php

namespace Feature\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthChangePasswordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_change_password(): void
    {
        $currentPassword = 'strongPassword123!';
        $newPassword = 'newPassword123!';

        $user = User::factory()->makeUser([
            'password' => $currentPassword,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('api.v1.auth.change-password'), [
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ]);

        $response->assertOk();

        $user->refresh();

        $this->assertTrue(Hash::check($newPassword, $user->password));
    }
}

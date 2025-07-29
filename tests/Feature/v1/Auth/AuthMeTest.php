<?php

namespace Feature\v1\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthMeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_me_returns_authenticated_user_resource()
    {
        $csrfResponse = $this->get('/sanctum/csrf-cookie');

        $csrfResponse->assertCookie('XSRF-TOKEN');
        $csrfResponse->assertCookie('laravel_session');

        $xsrfCookie = $csrfResponse->getCookie('XSRF-TOKEN');
        $sessionCookie = $csrfResponse->getCookie('laravel_session');

        $response = $this->withHeader('X-XSRF-TOKEN', $xsrfCookie->getValue())
            ->withCookie($xsrfCookie->getName(), $xsrfCookie->getValue())
            ->withCookie($sessionCookie->getName(), $sessionCookie->getValue())
            ->postJson(route('api.v1.auth.me'));

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'username',
            ]
        ]);

        $this->assertTrue(Auth::check());
    }
}

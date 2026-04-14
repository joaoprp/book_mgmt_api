<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    protected $authController;

    protected $req;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authController = new AuthController;
        $this->req = [
            'login' => Request::create('/api/login', 'POST', [
                'email' => 'joe@example.com',
                'password' => 'qwer1234',
            ]),
            'logout' => Request::create('/api/logout', 'POST'),
            'register' => Request::create('/api/register', 'POST', [
                'name' => 'Joe',
                'email' => 'joe@example.com',
                'password' => 'qwer1234',
                'password_confirmation' => 'qwer1234',
            ]),
        ];
    }

    public function test_register()
    {
        $response = $this->authController->register($this->req['register']);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_login()
    {
        User::factory()->create([
            'email' => 'joe@example.com',
            'password' => bcrypt('qwer1234'),
        ]);

        $response = $this->authController->login($this->req['login']);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_logout()
    {
        $user = User::factory()->create([
            'email' => 'joe@example.com',
            'password' => bcrypt('qwer1234'),
        ]);

        $this->authController->login($this->req['login']);

        $response = $this->actingAs($user)->post('/api/logout', [], ['Accept' => 'application/json']);

        $this->assertEquals(200, $response->getStatusCode());
    }
}

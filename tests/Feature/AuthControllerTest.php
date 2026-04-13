<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_register()
    {
        $this->post('api/register', [
            'name' => 'Joe',
            'email' => 'joe@example.com',
            'password' => 'qwer1234',
            'password_confirmation' => 'qwer1234',
        ])->assertStatus(201);
    }

    public function test_login()
    {
        // Register first
        $this->test_register();

        // Then login
        $this->post('api/login', [
            'email' => 'joe@example.com',
            'password' => 'qwer1234',
        ])->assertStatus(200);
    }

    public function test_logout()
    {
        // Register first
        $this->test_register();

        // Then login
        $response = $this->post('api/login', [
            'email' => 'joe@example.com',
            'password' => 'qwer1234',
        ]);

        $data = $response->json();

        // Then logout
        $this->post('api/logout', [], ['Authorization' => 'Bearer '.$data['token'], 'Accept' => 'application/json'])->assertStatus(200);
    }
}

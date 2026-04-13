<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use WithFaker;

    public function generate_user()
    {
        $email = $this->faker->email;
        $password = 'password';
        $this->post('api/register', [
            'name' => $this->faker->name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        return $this->post('api/login', [
            'email' => $email,
            'password' => $password,
        ], [], ['Accept' => 'application/json'])->json();
    }

    public function test_create_book()
    {
        $user = $this->generate_user();

        $response = $this->postJson('/api/books', [
            'title' => $this->faker->sentence,
            'pages' => $this->faker->numberBetween(1, 10),
        ], ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$user['token']]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'title',
            'pages',
            'created_at',
            'updated_at',
        ]);

        return $user;
    }

    public function test_get_books()
    {
        $user = $this->test_create_book();

        $response = $this->getJson('/api/books', ['Accept' => 'application/json', 'Authorization' => 'Bearer '.$user['token']]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'pages',
                'user',
                'created_at',
                'updated_at',
            ],
        ]);
    }
}

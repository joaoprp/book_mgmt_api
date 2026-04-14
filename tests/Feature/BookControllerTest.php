<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\BookController;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use WithFaker;

    private $bookController;

    private $req;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookController = new BookController;
        $this->user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
        ]);

        $this->actingAs($this->user);

        $this->req = [
            'get' => Request::create('/api/books', 'GET'),
            'post' => Request::create('/api/books', 'POST', [
                'title' => $this->faker->sentence,
                'pages' => $this->faker->numberBetween(1, 10),
            ]),
            'put' => Request::create('/api/books', 'PUT', [
                'indexes' => [
                    [
                        'title' => $this->faker->sentence,
                        'page' => $this->faker->numberBetween(1, 10),
                    ],
                ],
            ]),
        ];
    }

    public function test_create_book()
    {
        $response = $this->bookController->create($this->req['post']);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function test_get_books()
    {
        $book_length = 4;

        foreach (range(0, $book_length) as $_) {
            Book::create([
                'title' => $this->faker->sentence,
                'pages' => $this->faker->numberBetween(1, 10),
                'user_id' => $this->user->id,
            ]);
        }

        $response = $this->bookController->index($this->req['get']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertCount($book_length + 1, json_decode($response->getContent(), true));
    }

    public function test_edit_book()
    {
        $book = Book::create([
            'title' => $this->faker->sentence,
            'pages' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->user->id,
        ]);

        $response = $this->bookController->update($this->req['put'], $book->id);

        $book = Book::with('indexes')->find($book->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals(count($book->indexes), 1);
    }
}

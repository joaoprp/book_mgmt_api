<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\IndexController;
use App\Models\Book;
use App\Models\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use WithFaker;

    private $book;

    private $indexController;

    private $req;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->indexController = new IndexController;
        $this->user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
        ]);
        $this->book = Book::create([
            'title' => $this->faker->sentence,
            'pages' => $this->faker->numberBetween(1, 1000),
            'user_id' => $this->user->id,
        ]);

        $this->req = [
            'get' => Request::create('/api/index', 'GET'),
            'post' => Request::create('/api/index', 'POST', [
                'title' => $this->faker->sentence,
                'page' => $this->faker->numberBetween(1, 20),
                'book_id' => $this->book->id,
            ]),
            'put' => Request::create('/api/index', 'PUT', [
                'title' => $this->faker->sentence,
            ]),
        ];
    }

    public function test_index_returns_all_indexes()
    {
        $idx_length = 4;

        foreach (range(0, $idx_length) as $_) {
            Index::create([
                'title' => $this->faker->sentence,
                'page' => $this->faker->numberBetween(1, 20),
                'book_id' => $this->book->id,
            ]);
        }

        $response = $this->indexController->index($this->req['get']);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($response->getContent());

        $this->assertCount($idx_length + 1, json_decode($response->getContent(), true));
    }

    public function test_create_returns_created_index()
    {
        $res = $this->indexController->create($this->req['post']);

        $this->assertEquals(201, $res->getStatusCode());
        $this->assertJson($res->getContent());

    }

    public function test_update_returns_updated_index()
    {
        $created = $this->indexController->create($this->req['post'])->getOriginalContent();

        $res = $this->indexController->update($this->req['put'], $created['id']);

        $this->assertEquals(200, $res->getStatusCode());
        $this->assertJson($res->getContent());
    }

    public function test_destroy_returns_no_content()
    {
        $created = $this->indexController->create($this->req['post'])->getOriginalContent();

        $res = $this->indexController->destroy($created['id']);

        $this->assertEquals(204, $res->getStatusCode());
        $this->assertEmpty($res->getContent());
    }
}

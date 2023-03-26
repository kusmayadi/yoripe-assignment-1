<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostTest extends TestCase
{
    private $user;
    private $accessToken;

    public function setup(): void
    {
        parent::setup();

        $email = 'user@yoripe.com';
        $password = 'simplepassword';

        $this->user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $responseLogin = $this->post('/api/login', [
            'email' => $email,
            'password' => $password
        ]);

        $this->accessToken = $responseLogin->json('access_token');

    }

    public function test_index(): void
    {
        Post::factory(5)->create();

        $response = $this
            ->withToken($this->accessToken)
            ->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data.data');
    }

    public function test_validation(): void
    {
        $responseCreate = $this
            ->withToken($this->accessToken)
            ->json('POST', '/api/posts', []);

        $responseCreate->assertStatus(422);

        $response = $this
            ->withToken($this->accessToken)
            ->json('POST', '/api/posts', [
                'title' => fake()->words(3, true),
                'content' => fake()->paragraphs(2, true)
            ]);

        $id = $response->json('data.id');

        $responseUpdate = $this
            ->withToken($this->accessToken)
            ->json('PUT', '/api/posts/' . $id, []);

        $responseUpdate->assertStatus(422);
    }

    public function test_create(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $response = $this
            ->withToken($this->accessToken)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->assertEquals($title, $response->json('data')['title']);
        $this->assertEquals($content, $response->json('data')['content']);
        $this->assertEquals($status, $response->json('data')['status']);

        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'updated_by' => $this->user->id
        ]);
    }

    public function test_read(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data')['id'];

        $response = $this
            ->withToken($this->accessToken)
            ->get('/api/posts/' . $id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->assertEquals($title, $response->json('data')['title']);
        $this->assertEquals($content, $response->json('data')['content']);
        $this->assertEquals($status, $response->json('data')['status']);
    }

    public function test_update(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data')['id'];

        $updatedTitle = fake()->words(3, true);
        $updatedContent = fake()->paragraphs(3, true);
        $updatedStatus = fake()->randomElement([0, 1]);

        $response = $this
            ->withToken($this->accessToken)
            ->put('/api/posts/' . $id, [
                'title' => $updatedTitle,
                'content' => $updatedContent,
                'status' => $updatedStatus
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->asserTEquals($updatedTitle, $response->json('data')['title']);
        $this->assertEquals($updatedContent, $response->json('data')['content']);
        $this->assertEquals($updatedStatus, $response->json('data')['status']);

        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'title' => $updatedTitle,
            'content' => $updatedContent,
            'status' => $updatedStatus,
            'updated_by' => $this->user->id
        ]);
    }

    public function test_delete(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data')['id'];

        $response = $this->delete('/api/posts/' . $id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('posts', [
            'user_id' => $this->user->id,
            'title' => $title,
            'content' => $content,
            'status' => $status,
            'updated_by' => $this->user->id
        ]);
    }
}

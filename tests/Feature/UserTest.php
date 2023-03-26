<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    private $accessToken;

    public function setup(): void
    {
        parent::setup();

        $this->seed(RoleSeeder::class);

        $adminCredentials = [
            'email' => 'admin@yoripe.com',
            'password' => 'adminpassword'
        ];

        $admin = User::factory()->create([
            'email' => $adminCredentials['email'],
            'password' => Hash::make($adminCredentials['password'])
        ]);

        $loginAdmin = $this->post('/api/login', $adminCredentials);

        $this->accessToken = $loginAdmin->json('access_token');
    }

    public function test_index(): void
    {
        User::factory(5)->create();

        $response = $this
            ->withToken($this->accessToken)
            ->get('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(6, 'data.data');
    }

    public function test_create(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $response = $this
            ->withToken($this->accessToken)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->assertEquals($name, $response->json('data.name'));
        $this->assertEquals($email, $response->json('data.email'));

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function test_read(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $id = $responseCreate->json('data.id');

        $response = $this
            ->withToken($this->accessToken)
            ->get('/api/users/' . $id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->assertEquals($name, $response->json('data.name'));
        $this->assertEquals($email, $response->json('data.email'));
    }

    public function test_update(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $id = $responseCreate->json('data.id');

        $updatedName = fake()->name;
        $updatedEmail = fake()->unique()->safeEmail();
        $updatedPassword = 'passwordupdated';

        $response = $this
            ->withToken($this->accessToken)
            ->put('/api/users/' . $id, [
                'name' => $updatedName,
                'email' => $updatedEmail,
                'password' => $updatedPassword
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'errors', 'data']);

        $this->asserTEquals($updatedName, $response->json('data.name'));
        $this->assertEquals($updatedEmail, $response->json('data.email'));

        $this->assertDatabaseHas('users', [
            'name' => $updatedName,
            'email' => $updatedEmail,
        ]);
    }

    public function test_delete(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->withToken($this->accessToken)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $id = $responseCreate->json('data.id');

        $response = $this->delete('/api/users/' . $id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', [
            'name' => $name,
            'email' => $email
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    private $user;
    private $manager;
    private $admin;

    public function setup(): void
    {
        parent::setup();

        $this->seed(RoleSeeder::class);

        // User Setup
        $this->user = User::factory()->create([
            'email' => 'user@yoripe.com',
            'password' => Hash::make('userpassword')
        ]);

        $this->user->assignRole('user');

        // Manager Setup
        $this->manager = User::factory()->create([
            'email' => 'manager@yoripe.com',
            'password' => Hash::make('managerpassword')
        ]);

        $this->manager->assignRole('manager');

        // Admin Setup
        $this->admin = User::factory()->create([
            'email' => 'admin@yoripe.com',
            'password' => Hash::make('adminpassword')
        ]);

        $this->admin->assignRole('admin');
    }

    public function test_admin(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->actingAs($this->admin)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $responseCreate->assertStatus(200);

        $id = $responseCreate->json('data.id');

        $updatedName = fake()->name();
        $updatedEmail = fake()->unique()->safeEmail();
        $updatedPassword = 'updatedpassword';

        $responseUpdate = $this
            ->actingAs($this->admin)
            ->put('/api/users/' . $id, [
                'name' => $updatedName,
                'email' => $updatedEmail,
                'password' => $updatedPassword
            ]);

        $responseUpdate->assertStatus(200);

        $responseDelete = $this
            ->actingAs($this->admin)
            ->delete('/api/users/' . $id);

        $responseDelete->assertStatus(200);
    }

    public function test_manager(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->actingAs($this->manager)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $responseCreate->assertStatus(403);

        $responseCreateAdmin = $this
            ->actingAs($this->admin)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $id = $responseCreateAdmin->json('data.id');

        $updatedName = fake()->name();
        $updatedEmail = fake()->unique()->safeEmail();
        $updatedPassword = 'updatedpassword';

        $responseUpdate = $this
            ->actingAs($this->manager)
            ->put('/api/users/' . $id, [
                'name' => $updatedName,
                'email' => $updatedEmail,
                'password' => $updatedPassword
            ]);

        $responseUpdate->assertStatus(403);

        $responseDelete = $this
            ->actingAs($this->manager)
            ->delete('/api/users/' . $id);

        $responseDelete->assertStatus(403);
    }

    public function test_user(): void
    {
        $name = fake()->name();
        $email = fake()->unique()->safeEmail();
        $password = 'password';

        $responseCreate = $this
            ->actingAs($this->user)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $responseCreate->assertStatus(403);

        $responseCreateAdmin = $this
            ->actingAs($this->admin)
            ->post('/api/users', [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]
        );

        $id = $responseCreateAdmin->json('data.id');

        $updatedName = fake()->name();
        $updatedEmail = fake()->unique()->safeEmail();
        $updatedPassword = 'updatedpassword';

        $responseUpdate = $this
            ->actingAs($this->user)
            ->put('/api/users/' . $id, [
                'name' => $updatedName,
                'email' => $updatedEmail,
                'password' => $updatedPassword
            ]);

        $responseUpdate->assertStatus(403);

        $responseDelete = $this
            ->actingAs($this->user)
            ->delete('/api/users/' . $id);

        $responseDelete->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    private $owner;
    private $ownerCredentials;
    private $otherUser;
    private $otherUserCredentials;
    private $manager;
    private $managerCredentials;
    private $admin;
    private $adminCredentials;

    public function setup(): void
    {
        parent::setup();

        $this->seed(RoleSeeder::class);

        // Owner Setup
        $this->ownerCredentials = [
            'email' => 'owneruser@yoripe.com',
            'password' => 'ownerpassword'
        ];

        $this->owner = User::factory()->create([
            'email' => $this->ownerCredentials['email'],
            'password' => Hash::make($this->ownerCredentials['password'])
        ]);

        $this->owner->assignRole('user');

        // Other User Setup
        $this->otherUserCredentials = [
            'email' => 'otheruser@yoripe.com',
            'password' => 'otheruserpassword'
        ];

        $this->otherUser = User::factory()->create([
            'email' => $this->otherUserCredentials['email'],
            'password' => Hash::make($this->otherUserCredentials['password'])
        ]);

        $this->otherUser->assignRole('user');

        // Manager Setup
        $this->managerCredentials = [
            'email' => 'manager@yoripe.com',
            'password' => 'managerpassword'
        ];

        $this->manager = User::factory()->create([
            'email' => $this->managerCredentials['email'],
            'password' => Hash::make($this->managerCredentials['password'])

        ]);

        $this->manager->assignRole('manager');

        // Admin Setup
        $this->adminCredentials = [
            'email' => 'admin@yoripe.com',
            'password' => 'adminpassword'
        ];

        $this->admin = User::factory()->create([
            'email' => $this->adminCredentials['email'],
            'password' => Hash::make($this->adminCredentials['password'])
        ]);

        $this->admin->assignRole('admin');
    }

    public function test_owner(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->actingAs($this->owner)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data.id');

        $updatedTitle = fake()->words(3, true);
        $updatedContent = fake()->paragraphs(3, true);
        $updatedStatus = fake()->randomElement([0, 1]);

        $responseUpdate = $this
            ->actingAs($this->owner)
            ->put('/api/posts/' . $id, [
                'title' => $updatedTitle,
                'content' => $updatedContent,
                'status' => $updatedStatus
            ]);

        $responseUpdate->assertStatus(200);

        $responseDelete = $this
            ->actingAs($this->owner)
            ->delete('/api/posts/' . $id);

        $responseDelete->assertStatus(200);
    }

    public function test_other_user(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->actingAs($this->owner)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data.id');

        $responseView = $this
            ->actingAs($this->otherUser)
            ->json('GET', '/api/posts/' . $id);

        $responseView->assertStatus(403);

        $updatedTitle = fake()->words(3, true);
        $updatedContent = fake()->paragraphs(3, true);
        $updatedStatus = fake()->randomElement([0, 1]);

        $responseUpdate = $this
            ->actingAs($this->otherUser)
            ->put('/api/posts/' . $id, [
                'title' => $updatedTitle,
                'content' => $updatedContent,
                'status' => $updatedStatus
            ]);

        $responseUpdate->assertStatus(403);

        $responseDelete = $this
            ->actingAs($this->otherUser)
            ->delete('/api/posts/' . $id);

        $responseDelete->assertStatus(403);
    }

    public function test_manager(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->actingAs($this->owner)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data.id');

        $responseView = $this
            ->actingAs($this->manager)
            ->json('GET', '/api/posts/' . $id);

        $responseView->assertStatus(200);

        $updatedTitle = fake()->words(3, true);
        $updatedContent = fake()->paragraphs(3, true);
        $updatedStatus = fake()->randomElement([0, 1]);

        $responseUpdate = $this
            ->actingAs($this->manager)
            ->put('/api/posts/' . $id, [
                'title' => $updatedTitle,
                'content' => $updatedContent,
                'status' => $updatedStatus
            ]);

        $responseUpdate->assertStatus(200);

        $responseDelete = $this
            ->actingAs($this->manager)
            ->delete('/api/posts/' . $id);

        $responseDelete->assertStatus(200);
    }

    public function test_admin(): void
    {
        $title = fake()->words(3, true);
        $content = fake()->paragraphs(3, true);
        $status = fake()->randomElement([0, 1]);

        $responseCreate = $this
            ->actingAs($this->owner)
            ->post('/api/posts', [
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]
        );

        $id = $responseCreate->json('data.id');

        $responseView = $this
            ->actingAs($this->admin)
            ->json('GET', '/api/posts/' . $id);

        $responseView->assertStatus(200);

        $updatedTitle = fake()->words(3, true);
        $updatedContent = fake()->paragraphs(3, true);
        $updatedStatus = fake()->randomElement([0, 1]);

        $responseUpdate = $this
            ->actingAs($this->admin)
            ->put('/api/posts/' . $id, [
                'title' => $updatedTitle,
                'content' => $updatedContent,
                'status' => $updatedStatus
            ]);

        $responseUpdate->assertStatus(200);

        $responseDelete = $this
            ->actingAs($this->admin)
            ->delete('/api/posts/' . $id);

        $responseDelete->assertStatus(200);
    }
}

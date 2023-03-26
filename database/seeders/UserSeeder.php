<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        $admin = User::factory()->create([
            'email' => 'admin@yoripe.com',
            'password' => Hash::make('!OrdinaryAdmin')
        ]);

        $admin->assignRole('admin');

        if (config('app.env') != 'production') {
            // manager
            $manager = User::factory()->create([
                'email' => 'manager@yoripe.com',
                'password' => Hash::make('!OrdinaryManager')
            ]);

            $manager->assignRole('manager');

            // user
            $user = User::factory()->create([
                'email' => 'user@yoripe.com',
                'password' => Hash::make('JustOrdinaryUser#1')
            ]);

            $user->assignRole('user');
        }
    }
}

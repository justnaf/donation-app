<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        $donators = User::factory(5)->create();
        $donators->each(function ($user) {
            $user->assignRole('donatur');
        });
    }
}

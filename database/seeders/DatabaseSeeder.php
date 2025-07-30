<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a landlord (admin) user
        User::factory()->create([
            'name' => 'beverline Landlord',
            'email' => 'landlord@astraspaces.com',
            'role' => 'landlord',
            'password' => bcrypt('password123')
        ]);

        // Create tenant users
        User::factory()->create([
            'name' => 'Jane Tenant',
            'email' => 'tenant@astraspaces.com',
            'role' => 'tenant',
            'password' => bcrypt('password123')
        ]);

        User::factory()->create([
            'name' => 'Mike Tenant',
            'email' => 'mike@astraspaces.com',
            'role' => 'tenant',
            'password' => bcrypt('password123')
        ]);
    }
}

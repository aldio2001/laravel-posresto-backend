<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 20 random users
        User::factory(20)->create();

        // Create a specific user with given attributes
        User::factory()->create([
            'name' => 'Aldio Yohanes',
            'email' => 'aldioguire@gmail.com',
            'password' => Hash::make('12345678'),
            'roles' => 'admin', // Ensure 'roles' is fillable in the User model
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}

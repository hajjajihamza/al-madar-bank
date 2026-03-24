<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'first_name' => 'Hamza',
            'last_name' => 'Hajajji',
            'email' => 'hamza@gmail.com',
        ]);

        $this->call([
            AccountSeeder::class,
        ]);
    }
}

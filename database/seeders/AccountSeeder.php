<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::factory(5)->courant()->create();
        Account::factory(3)->epargne()->create();

        // 1 compte mineur
        $user = User::where('email', 'hamza@gmail.com')->first();
        if ($user) {
            $minorUser = User::factory()->create([
                'first_name' => 'Junior',
                'last_name' => 'Salami',
                'email' => 'junior@gmail.com',
                'date_of_birth' => '2010-01-01',
            ]);

            Account::factory()->mineur()->create()->users()->attach([
                $minorUser->id => ['guardian_id' => $user->id]
            ]);
        }

        // Create a blocked account for the first user
        Account::factory(2)->blocked()->create();
    }
}

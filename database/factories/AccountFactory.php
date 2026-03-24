<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\AccountStatus;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Account $account) {
            if ($account->users()->count() === 0 && $account->type !== AccountType::MINEUR) {
                $account->users()->attach(User::factory()->create());
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(AccountType::cases());

        return [
            'number' => $this->faker->unique()->iban('MA'),
            'type' => $type,
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'interest_rate' => $this->faker->randomFloat(2, 0.5, 5.0)
        ];
    }

    public function courant(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AccountType::COURANT,
            'overdraft_limit' => $this->faker->randomFloat(2, 0, 1000),
            'interest_rate' => 0
        ]);
    }

    public function epargne(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AccountType::EPARGNE,
            'monthly_withdrawals' => 3,
        ]);
    }

    public function mineur(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => AccountType::MINEUR,
            'monthly_withdrawals' => 2,
            'status' => AccountStatus::ACTIVE,
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AccountStatus::BLOCKED,
            'blocked_reason' => $this->faker->sentence(),
        ]);
    }
}

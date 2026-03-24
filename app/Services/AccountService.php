<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountService
{
    /**
     * Create a new account and attach it to the user.
     *
     * @param array $data
     * @return Account
     */
    public static function createAccount(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            $type = $data['type'];
            $config = self::getAccountConfig($type);

            $account = Account::create(
                array_merge($config, [
                    'number' => self::generateAccountNumber(),
                    'type' => $type,
                ])
            );

            $account->users()->syncWithoutDetaching(
                auth()->id(), [
                    'guardian_id' => $data['guardian_id'] ?? null,
                ],
            );

            return $account;
        });
    }

    /**
     * Get default configuration for account type.
     *
     * @param string $type
     * @return array
     */
    protected static function getAccountConfig(string $type): array
    {
        return match ($type) {
            AccountType::COURANT->value => [
                'overdraft_limit' => 200,
                'monthly_withdrawals' => null,
                'interest_rate' => 0,
            ],
            AccountType::EPARGNE->value => [
                'overdraft_limit' => 0,
                'monthly_withdrawals' => 3,
                'interest_rate' => 3.5,
            ],
            AccountType::MINEUR->value => [
                'overdraft_limit' => 0,
                'monthly_withdrawals' => 2,
                'interest_rate' => 3.5,
            ]
        };
    }

    /**
     * Generate a unique account number.
     *
     * @return string
     */
    protected static function generateAccountNumber(): string
    {
        do {
            $number = Str::upper(Str::random(10));
        } while (Account::where('number', $number)->exists());

        return $number;
    }
}

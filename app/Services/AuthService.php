<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
        ]);
    }

    /**
     * Authenticate user and return token.
     *
     * @param array $credentials
     * @return string|bool
     */
    public function login(array $credentials): string|bool
    {
        if (!$token = Auth::attempt($credentials)) {
            return false;
        }

        return $token;
    }

    /**
     * Authenticate user with Google and return token.
     *
     * @return string
     */
    public function authGoogle(): string
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if user exists
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Create new user
            [$firstName, $lastName] = explode(' ', $googleUser->getName(),2);
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(uniqid('', true)), // random password
                'date_of_birth' => date('Y-m-d'),
            ]);
        }

        return JWTAuth::fromUser($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Refresh a token.
     *
     * @return string
     */
    public function refresh(): string
    {
        return Auth::refresh();
    }

    /**
     * Get the authenticated user.
     *
     * @return User
     */
    public function me(): User
    {
        return Auth::user();
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    /**
     * Change user password.
     *
     * @param User $user
     * @param string $newPassword
     * @return void
     */
    public function changePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}

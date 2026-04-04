<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ChangePasswordRequest;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get the authenticated user's profile.
     */
    public function me(): JsonResponse
    {
        return response()->json($this->authService->me()->toResource());
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $this->authService->updateProfile(
            $this->authService->me(),
            $request->validated()
        );

        return new UserResource($user);
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword(
            $this->authService->me(),
            $request->password
        );

        return response()->json(['message' => 'Password changed successfully']);
    }
}

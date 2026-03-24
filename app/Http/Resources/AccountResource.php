<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'balance' => $this->balance,
            'overdraft_limit' => $this->overdraft_limit,
            'interest_rate' => $this->interest_rate,
            'status' => $this->status,
            'blocked_reason' => $this->blocked_reason,
            'monthly_withdrawals' => $this->monthly_withdrawals,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(fn($user) => new UserResource($user));
            }),
            'guardians' => $this->whenLoaded('guardians', function () {
                return $this->guardians->map(fn($guardian) => new UserResource($guardian));
            }),
        ];
    }
}

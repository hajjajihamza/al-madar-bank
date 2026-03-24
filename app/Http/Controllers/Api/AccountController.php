<?php

namespace App\Http\Controllers\Api;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\StoreAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $accounts = auth()->user()->accounts()->with('users')->get();
        $guardianAccounts = auth()->user()->guardianAccounts()->with('users')->get();

        return AccountResource::collection($accounts->concat($guardianAccounts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request): AccountResource
    {
        $account = AccountService::createAccount($request->validated());

        return new AccountResource($account->load(['users', 'guardians']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        // Simple authorization check: user must belong to the account
        if ($account->type === AccountType::MINEUR) {
            if (! $account->users()->wherePivot('user_id', auth()->id())->exists() && ! $account->guardians()->wherePivot('guardian_id', auth()->id())->exists()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if (! $account->users()->wherePivot('user_id', auth()->id())->exists()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return new AccountResource($account->load(['users', 'guardians']));
    }
}

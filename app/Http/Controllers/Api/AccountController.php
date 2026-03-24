<?php

namespace App\Http\Controllers\Api;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\StoreAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    public function __construct(private AccountService $accountService) {}

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
        $account = $this->accountService->createAccount($request->validated());

        return new AccountResource($account->load(['users', 'guardians']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        if (!$this->checkAuthorization($account)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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


    /**
     * Add co-owners to the specified account.
     */
    public function addCoOwner(Account $account, User $user): JsonResponse
    {
        if (!$this->checkAuthorization($account)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot add yourself as a co-owner'], 403);
        }

        if ($account->users()->wherePivot('user_id', $user->id)->exists() || $account->guardians()->wherePivot('guardian_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already a co-owner'], 403);
        }

        $this->accountService->addCoOwner($account, $user->id);

        return response()->json([
            'message' => 'Co-owners added successfully',
            'account' => new AccountResource($account->load(['users', 'guardians'])),
        ]);
    }

    public function removeCoOwner(Account $account, User $user)
    {
        if (!$this->checkAuthorization($account)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot remove yourself as a co-owner'], 403);
        }

        if (!$account->users()->wherePivot('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is not a co-owner'], 403);
        }

        $this->accountService->removeCoOwner($account, $user->id);

        return response()->json([
            'message' => 'Co-owners removed successfully',
            'account' => new AccountResource($account->load(['users', 'guardians'])),
        ]);
    }

    public function convertMinorAccountToCourant(Account $account)
    {
        if (!$account->guardians()->wherePivot('guardian_id', auth()->id())->exists()) {
            return response()->json(['message' => 'You are not authorized to convert this account'], 403);
        }

        if ($account->type !== AccountType::MINEUR) {
            return response()->json(['message' => 'Account is not a minor account'], 403);
        }

        if ($account->users()->first()->age < 18) {
            return response()->json(['message' => 'You cannot convert this account because the user is not an adult'], 403);
        }

        $this->accountService->convertMinorAccountToCourant($account);

        return response()->json([
            'message' => 'Minor account converted to courant successfully',
            'account' => new AccountResource($account->load(['users'])),
        ]);
    }

    public function demandeCloseAccount(Account $account)
    {
        if (!$this->checkAuthorization($account)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->accountService->demandeCloseAccount($account);

        return response()->json([
            'message' => 'Account closure requested successfully',
            'account' => new AccountResource($account->load(['users'])),
        ]);
    }

    protected function checkAuthorization(Account $account): bool
    {
        if ($account->type === AccountType::MINEUR) {
            if (! $account->users()->wherePivot('user_id', auth()->id())->exists() && ! $account->guardians()->wherePivot('guardian_id', auth()->id())->exists()) {
                return false;
            }
        } else {
            if (! $account->users()->wherePivot('user_id', auth()->id())->exists()) {
                return false;
            }
        }

        return true;
    }
}

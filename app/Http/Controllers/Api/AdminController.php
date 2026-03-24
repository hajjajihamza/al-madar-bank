<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Enums\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * Get all accounts
     */
    public function index(): JsonResponse
    {
        // Fetch accounts, possibly with their users for context
        $accounts = Account::with('users')->get();
        return response()->json($accounts);
    }

    /**
     * Block an account
     */
    public function blockAccount(Account $account, Request $request): JsonResponse
    {
        $account->update([
            'status' => AccountStatus::BLOCKED,
            'blocked_reason' => $request->input('blocked_reason', 'Blocked by admin'),
        ]);

        return response()->json([
            'message' => 'Account blocked successfully',
            'account' => $account
        ]);
    }

    /**
     * Unblock an account
     */
    public function unblockAccount(Account $account): JsonResponse
    {
        $account->update([
            'status' => AccountStatus::ACTIVE,
            'blocked_reason' => null,
        ]);

        return response()->json([
            'message' => 'Account unblocked successfully',
            'account' => $account
        ]);
    }

    /**
     * Close an account
     */
    public function closeAccount(Account $account): JsonResponse
    {
        $account->update([
            'status' => AccountStatus::CLOSED,
        ]);

        return response()->json([
            'message' => 'Account closed successfully',
            'account' => $account
        ]);
    }
}

<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('refresh', 'refresh')->middleware('auth:api');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

Route::middleware('auth:api')->controller(UserController::class)->group(function () {
    Route::get('users/me', 'me');
    Route::patch('users/me', 'update');
    Route::patch('users/me/password', 'changePassword');
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('accounts', AccountController::class)->only(['index', 'store', 'show']);
    Route::post('accounts/{account}/co-owners/{user}', [AccountController::class, 'addCoOwner']);
    Route::delete('accounts/{account}/co-owners/{user}', [AccountController::class, 'removeCoOwner']);
    Route::patch('accounts/{account}/convert-minor-to-courant', [AccountController::class, 'convertMinorAccountToCourant']);
    Route::patch('accounts/{account}/demande-close', [AccountController::class, 'demandeCloseAccount']);
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('accounts', [AdminController::class, 'index']);
    Route::patch('accounts/{account}/block', [AdminController::class, 'blockAccount']);
    Route::patch('accounts/{account}/unblock', [AdminController::class, 'unblockAccount']);
    Route::patch('accounts/{account}/close', [AdminController::class, 'closeAccount']);
});

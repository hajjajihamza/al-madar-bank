<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AccountType;
use App\Enums\AccountStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->enum('type', [AccountType::COURANT, AccountType::EPARGNE, AccountType::MINEUR]);
            $table->float('balance')->default(0);
            $table->float('overdraft_limit')->default(0);
            $table->float('interest_rate')->default(0);
            $table->enum('status', [AccountStatus::ACTIVE, AccountStatus::BLOCKED, AccountStatus::CLOSED])->default(AccountStatus::ACTIVE);
            $table->text('blocked_reason')->nullable();
            $table->integer('monthly_withdrawals')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

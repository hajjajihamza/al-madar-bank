<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', // identifier unique de compte bancaire
        'type', // type de compte bancaire (compte courant, compte épargne, compte joint)
        'balance', // solde du compte bancaire
        'overdraft_limit', // limite de découvert du compte bancaire
        'interest_rate', // taux d'intérêt du compte bancaire
        'status', // statut du compte bancaire (actif, inactif, bloqué)
        'blocked_reason', // raison de blocage du compte bancaire
        'monthly_withdrawals', // (nullable) nombre de retraits mensuels du compte bancaire
    ];

    protected $casts = [
        'type' => AccountType::class,
        'status' => AccountStatus::class,
        'balance' => 'float',
        'overdraft_limit' => 'float',
        'interest_rate' => 'float',
        'monthly_withdrawals' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contracts', 'account_id', 'user_id')
            ->withPivot('guardian_id', 'accepted_closure')
            ->withTimestamps();
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contracts', 'account_id', 'guardian_id')
            ->withPivot('user_id', 'accepted_closure')
            ->withTimestamps();
    }
}

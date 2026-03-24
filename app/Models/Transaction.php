<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TransactionType;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'creator_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

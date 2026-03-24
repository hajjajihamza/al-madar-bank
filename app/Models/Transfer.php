<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TransferStatus;

class Transfer extends Model
{
    protected $fillable = [
        'source_id',
        'destination_id',
        'creator_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'status' => TransferStatus::class,
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

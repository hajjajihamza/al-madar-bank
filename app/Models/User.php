<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'is_admin',
        'date_of_birth',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var list<string>
     */
    protected $appends = ['age'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->is_admin ? 'admin' : 'user',
        ];
    }

    /**
     * Get the accounts associated with the user.
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'contracts')
            ->withPivot('guardian_id', 'accepted_closure')
            ->withTimestamps();
    }

    public function guardianAccounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'contracts', 'guardian_id')
            ->withPivot('user_id', 'accepted_closure')
            ->withTimestamps();
    }

    /**
     * Calculate user age.
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->date_of_birth)->age,
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'creator_id');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'creator_id');
    }
}

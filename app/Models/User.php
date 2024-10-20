<?php

namespace App\Models;

use App\Traits\HasRepositoryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, HasRepositoryTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * @var array|string[]
     */
    public array $relationships = [];

    /**
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function wallet_histories(): HasMany
    {
        return $this->hasMany(UserWalletTransaction::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    /**
     * @return int
     */
    public function getFirstName(): string
    {
        return $this->attributes['first_name'];
    }

    /**
     * @return int
     */
    public function getLastName(): string
    {
        return $this->attributes['last_name'];
    }

    /**
     * @return float
     */
    public function getEmail(): string
    {
        return $this->attributes['email'];
    }
}

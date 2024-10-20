<?php

namespace App\Models;

use App\Traits\HasRepositoryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWalletTransaction extends Model
{
    use HasFactory, HasRepositoryTrait;

    /**
     * @var array|string[]
     */
    public array $relationships = [
        'user',
        'transaction',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
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
    public function getUserId(): int
    {
        return $this->attributes['user_id'];
    }

    /**
     * @return float
     */
    public function getPreviousBalance(): float
    {
        return $this->attributes['previous_balance'];
    }

    /**
     * @return float
     */
    public function getNewBalance(): float
    {
        return $this->attributes['new_balance'];
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return User|null
     */
    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }
}

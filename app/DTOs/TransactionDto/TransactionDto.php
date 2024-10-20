<?php
namespace App\DTOs\TransactionDto;


use App\Models\User;

class TransactionDto
{
    /**
     * @param int $amount
     */
    public function __construct(
        public int $amount
    ) { }

    /**
     * @param int $amount
     * @return self
     */
    public static function TransactionDto(int $amount): self
    {
        return new self($amount);
    }
}

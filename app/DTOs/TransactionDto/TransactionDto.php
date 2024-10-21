<?php
namespace App\DTOs\TransactionDto;


use App\Models\User;

class TransactionDto
{
    /**
     * @param int $amount
     * @param string $type
     */
    public function __construct(
        public int $amount,
        public string $type = "Credit"
    ) { }

    /**
     * @param int $amount
     * @param string $type
     * @return self
     */
    public static function TransactionDto(int $amount, string $type): self
    {
        return new self($amount, $type);
    }
}

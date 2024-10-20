<?php

namespace App\DTOs\AuthDto;

class LoginDto
{
    /**
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $email,
        public string $password
    ) { }

    /**
     * @param string $email
     * @param string $password
     * @return self
     */
    public static function loginDto(string $email, string $password): self
    {
        return new self($email, $password);
    }
}

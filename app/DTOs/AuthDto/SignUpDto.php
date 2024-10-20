<?php
namespace App\DTOs\AuthDto;


class SignUpDto
{
    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $firstname,
        public string $lastname,
        public string $email,
        public string $password
    ) { }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @return self
     */
    public static function signUpDto(string $firstname, string $lastname, string $email, string $password): self
    {
        return new self($firstname ,$lastname , $email, $password);
    }
}

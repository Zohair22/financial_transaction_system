<?php

namespace Modules\User\DTO;

class AuthDTO
{
    public function __construct(
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?string $name = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'] ?? null,
            name: $data['name'] ?? null,
        );
    }
}

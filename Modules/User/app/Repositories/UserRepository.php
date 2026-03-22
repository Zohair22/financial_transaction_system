<?php

namespace Modules\User\Repositories;

use Modules\User\DTO\AuthDTO;
use Modules\User\Interfaces\IUserRepository;
use Modules\User\Models\User;

class UserRepository implements IUserRepository
{
    public function create(AuthDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}

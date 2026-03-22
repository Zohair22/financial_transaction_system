<?php

namespace Modules\User\Interfaces;

use Modules\User\DTO\AuthDTO;
use Modules\User\Models\User;

interface IUserRepository {
    public function create(AuthDTO $dto): User;
    public function findByEmail(string $email): ?User;
}

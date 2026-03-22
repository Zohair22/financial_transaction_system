<?php

namespace Modules\User\Interfaces;
use Modules\User\DTO\AuthDTO;

interface IAuthService {
    public function register(AuthDTO $dto): array;
    public function login(AuthDTO $dto): array;
}

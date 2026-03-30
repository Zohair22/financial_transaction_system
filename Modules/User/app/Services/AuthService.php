<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Hash;
use Modules\User\DTO\AuthDTO;
use Modules\User\Interfaces\IAuthService;
use Modules\User\Interfaces\IUserRepository;

class AuthService implements IAuthService
{
    public function __construct(
        protected IUserRepository $userRepository
    ) {}

    public function register(AuthDTO $dto): array
    {
        $dto = new AuthDTO(
            email: $dto->email,
            password: Hash::make($dto->password),
            name: $dto->name
        );

        $user = $this->userRepository->create($dto);

        $token = $user->createToken('api_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function login(AuthDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = $user->createToken('api_token', ['*'], now()->addDays(3))->plainTextToken;

        return compact('user', 'token');
    }
}

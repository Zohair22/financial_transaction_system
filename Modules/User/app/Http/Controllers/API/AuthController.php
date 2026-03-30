<?php

namespace Modules\User\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\LoginRequest;
use Modules\User\Http\Requests\RegisterRequest;
use Modules\User\Interfaces\IAuthService;

class AuthController extends Controller
{
    public function __construct(
        protected IAuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        return response()->json(
            $this->authService->register($dto)
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        return response()->json(
            $this->authService->login($dto)
        );
    }
}

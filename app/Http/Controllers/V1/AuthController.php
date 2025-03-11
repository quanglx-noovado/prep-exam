<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Src\Application\Auth\Commands\LoginCommand;
use Src\Application\Auth\Commands\LoginCommandHandler;

class AuthController extends BaseController
{
    public function __construct(
        private readonly LoginCommandHandler $loginHandler
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $command = new LoginCommand(
            email: $request->input('email'),
            password: $request->input('password')
        );

        $token = $this->loginHandler->handle($command);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}

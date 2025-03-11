<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use League\Tactician\CommandBus;
use Src\Application\Auth\Command\LoginCommand;

class AuthController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {

    }

    public function login(Request $request): JsonResponse
    {
        $command = new LoginCommand(
            email: $request->input('email'),
            password: $request->input('password')
        );

        $token = $this->commandBus->handle($command);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}

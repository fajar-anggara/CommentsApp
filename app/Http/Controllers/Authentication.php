<?php

namespace App\Http\Controllers;

use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Helpers\Interfaces\LogHelper;
use App\Http\Requests\CommenterRegisterRequest;
use App\Repositories\Interfaces\AuthenticationRepository;
use Illuminate\Http\JsonResponse;

class Authentication
{

    protected AuthenticationRepository $auth;
    protected LogHelper $log;
    public function __construct(
        AuthenticationRepository $auth,
    )
    {
        $this->auth = $auth;
    }

    public function register(CommenterRegisterRequest $request): JsonResponse
    {
        SetLog::witHEvent('Register Commenter')
            ->withProperties([
                'name' => $request->get('name'),
            ])
            ->withMessage('Prepare to register a new commenter')
            ->build();

        $validated = $request->validated();
        $commenter = $this->auth->addNewCommenter($validated);
        $token = $commenter->createToken('register_commenter')->plainTextToken;

        $data = Fractal::useCommenterTransformer($commenter, $token)
            ->withIncludes(['details'])
            ->buildWithArraySerializer();

        SetLog::withEvent('Register Commenter')
            ->withProperties([
                'name' => $commenter->name,
                'email' => $commenter->email,
                'token' => $token,
            ])
            ->performedOn($commenter)
            ->withMessage('commenter registered')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil register Commenter',
            'data' => $data,
        ], 201);
    }

    public function login()
    {

    }

    public function logout()
    {

    }

    public function refresh()
    {

    }

    public function forgotPassword()
    {

    }
}

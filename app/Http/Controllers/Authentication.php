<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Exceptions\WrongCredentialsException;
use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommenterLoginRequest;
use App\Http\Requests\CommenterRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class Authentication
{

    public function register(CommenterRegisterRequest $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::REGISTER)
            ->causedBy(Arr::only($request->validated(), ['email', 'name']))
            ->performedOn(Authentication::class)
            ->withMessage('Prepare to register a new commenter')
            ->build();

        $validated = $request->validated();
        $commenter = AuthDo::addNewCommenter($validated);
        $token = $commenter->createToken('register_commenter')->plainTextToken;

        $data = Fractal::useCommenterTransformer($commenter, $token)
            ->withIncludes(['details'])
            ->buildWithArraySerializer();

        SetLog::withEvent(LogEvents::REGISTER)
            ->causedBy(Arr::only($validated, ['name', 'email']))
            ->performedOn(Authentication::class)
            ->withMessage('Commenter registered successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil register Commenter',
            'data' => $data,
        ], 201);
    }

    /**
     * @throws WrongCredentialsException
     */
    public function login(CommenterLoginRequest $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::LOGIN)
            ->causedBy(Arr::only($request->validated(), ['email']))
            ->performedOn(Authentication::class)
            ->withMessage('Prepare to login a commenter')
            ->build();

        $validated = $request->validated();
        $commenter = AuthDo::findCommenterByEmail($validated['email']);

        if (!$commenter || !Hash::check($validated['password'], $commenter->password)) {
            SetLog::withEvent(LogEvents::LOGIN)
                ->withProperties(['email' => $validated["email"]])
                ->performedOn(Authentication::class)
                ->withMessage('Commenter login failed: wrong email or password')
                ->build();

            throw new WrongCredentialsException($validated['email']);
        }

        $commenter->tokens()->delete();
        $token = $commenter->createToken('login_commenter')->plainTextToken;
        $data = Fractal::useCommenterTransformer($commenter, $token)
            ->buildWithArraySerializer();

        SetLog::withEvent(LogEvents::LOGIN)
            ->causedBy(Arr::only($validated, ['name', 'email']))
            ->performedOn(Authentication::class)
            ->withMessage('Commenter login successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil login',
            'data' => $data,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::LOGOUT)
            ->causedBy(['name' => $request->user()])
            ->performedOn(Authentication::class)
            ->withMessage('Prepare Logout Commenter')
            ->build();

        $request->user()->currentAccessToken()->delete();

        SetLog::withEvent(LogEvents::LOGOUT)
            ->causedBy(['name' => $request->user()])
            ->performedOn(Authentication::class)
            ->withMessage('Commenter logout successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::REFRESH_TOKEN)
            ->causedBy(['name' => $request->user()])
            ->performedOn(Authentication::class)
            ->withMessage('Prepare refresh token')
            ->build();

        $request->user()->tokens()->delete();
        $token = $request->user()->createToken('refresh_token')->plainTextToken;

        SetLog::withEvent(LogEvents::REFRESH_TOKEN)
            ->causedBy(['name' => $request->user()])
            ->performedOn(Authentication::class)
            ->withMessage('Refresh token successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil refresh token',
            'data' => [
                'token' => $token,
            ]
        ]);
    }

//    public function forgotPassword()
//    {
//
//    }
}

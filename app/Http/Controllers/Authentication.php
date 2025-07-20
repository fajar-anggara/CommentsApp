<?php

namespace App\Http\Controllers;

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
        SetLog::withEvent('Register')
            ->causedBy(Arr::only($request->validated(), ['email']))
            ->withProperties([
                'name' => $request->get('name'),
                'time' => now()
            ])
            ->withMessage('Prepare to register a new commenter')
            ->build();

        $validated = $request->validated();
        $commenter = AuthDo::addNewCommenter($validated);
        $token = $commenter->createToken('register_commenter')->plainTextToken;

        $data = Fractal::useCommenterTransformer($commenter, $token)
            ->withIncludes(['details'])
            ->buildWithArraySerializer();

        SetLog::withEvent('Register')
            ->causedBy(Arr::only($validated, ['name', 'email']))
            ->withProperties([
                'time' => now()
            ])
            ->performedOn(User::class)
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
        SetLog::withEvent('Login')
            ->causedBy(Arr::only($request->validated(), ['email']))
            ->withProperties([
                'time' => now()
            ])
            ->withMessage('Prepare to login a commenter')
            ->build();

        $validated = $request->validated();
        $commenter = AuthDo::findCommenterByEmail($validated['email']);

        if (!$commenter || !Hash::check($validated['password'], $commenter->password)) {
            SetLog::withEvent('Login Commenter')
                ->withProperties(['email' => $validated["email"]])
                ->withMessage('Commenter login failed: wrong email or password')
                ->build();

            throw new WrongCredentialsException($validated['email']);
        }

        $commenter->tokens()->delete();
        $token = $commenter->createToken('login_commenter')->plainTextToken;
        $data = Fractal::useCommenterTransformer($commenter, $token)
            ->buildWithArraySerializer();

        SetLog::withEvent('Login')
            ->causedBy(Arr::only($validated, ['name', 'email']))
            ->withProperties([
                'time' => now()
            ])
            ->performedOn(User::class)
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
        SetLog::withEvent('Logout')
            ->causedBy(['name' => $request->user()])
            ->withProperties([
                'time' => now()
            ])
            ->withMessage('Prepare Logout Commenter')
            ->build();

        $request->user()->currentAccessToken()->delete();

        SetLog::withEvent('Logout')
            ->causedBy(['name' => $request->user()])
            ->withProperties([
                'time' => now()
            ])
            ->withMessage('Commenter logout successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        SetLog::withEvent('Refresh Token')
            ->causedBy(['name' => $request->user()])
            ->withProperties([
                'refresh_at' => now()
            ])
            ->withMessage('Prepare refresh token')
            ->build();

        $request->user()->tokens()->delete();
        $token = $request->user()->createToken('refresh_token')->plainTextToken;

        SetLog::withEvent('Refresh Token')
            ->causedBy(['name' => $request->user()])
            ->withProperties([
                'refresh_at' => now()
            ])
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

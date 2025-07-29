<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Exceptions\CommenterExceptions\WrongCredentialsException;
use App\Facades\Auth;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommenterLoginRequest;
use App\Http\Requests\CommenterRegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class Authentication
{

    /**
     * Register a new commenter
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Authentication"},
     *     summary="Register a new commenter",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil register Commenter"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="01982faf-bf2e-7050-8c0a-cdef6db19460"),
     *                 @OA\Property(property="name", type="string", example="andriana"),
     *                 @OA\Property(property="avatar_url", type="string", example=""),
     *                 @OA\Property(property="token", type="string", example="3|LUx4smphAFtNOP5c47cdB6gBdOMpaCEw7YQwEog152cc13de"),
     *                 @OA\Property(
     *                     property="details",
     *                     type="object",
     *                     @OA\Property(property="email", type="string", format="email", example="andriana@gmail.com"),
     *                     @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *                     @OA\Property(property="bio", type="string", example=""),
     *                     @OA\Property(property="total_comments_created", type="integer", example=0),
     *                     @OA\Property(property="total_likes_acquired", type="integer", example=0),
     *                     @OA\Property(property="is_muted", type="boolean", example=false),
     *                     @OA\Property(property="is_banned", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-22T01:11:43+00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Kesalahan. Silakan coba lagi",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kesalahan. Silakan coba lagi")
     *         )
     *     )
     * )
     */

    public function register(CommenterRegisterRequest $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'register'
                ]
            ])
            ->withMessage('Prepare to register a new commenter')
            ->build();

        $commenter = Auth::register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil register Commenter',
            'data' => Fractal::useCommenterTransformer($commenter['data'], $commenter['token'])
                ->withIncludes(['details'])
                ->buildWithArraySerializer(),
        ], 201);
    }

    /**
     * Login commenter and retrieve bearer token
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login commenter",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil login"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="01982faf-bf2e-7050-8c0a-cdef6db19460"),
     *                 @OA\Property(property="name", type="string", example="andriana"),
     *                 @OA\Property(property="avatar_url", type="string", example=""),
     *                 @OA\Property(property="token", type="string", example="4|LUx4smphAFtNOP5c47cdB6gBdOMpaCEw7YQwEog152cc13de")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Email atau password salah")
     *         )
     *     )
     * )
     */
    public function login(CommenterLoginRequest $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'login'
                ]
            ])
            ->withMessage('Prepare to login a commenter')
            ->build();

        $commenter = Auth::login($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil login',
            'data' => Fractal::useCommenterTransformer(
                $commenter['data'],
                $commenter['token']
            )->buildWithArraySerializer()
        ]);
    }

    /**
     * Logout authenticated commenter
     *
     * @OA\Get(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout commenter",
     *     security={{{"sanctum":{}}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil logout")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'logout'
                ]
            ])
            ->withMessage('Prepare Logout Commenter')
            ->build();

        $request->user()->currentAccessToken()->delete();

        SetLog::withEvent(LogEvents::LOGOUT)
            ->causedBy($request->user())
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'logout'
                ]
            ])
            ->withMessage('Commenter logout successfully')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
        ]);
    }

    /**
     * Refresh bearer token for authenticated commenter
     *
     * @OA\Get(
     *     path="/api/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh token",
     *     security={{{"sanctum":{}}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil refresh token"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="5|someNewRefreshedToken")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'refresh'
                ]
            ])
            ->withMessage('Prepare refresh token')
            ->build();

        $request->user()->tokens()->delete();
        $token = $request->user()->createToken('refresh_token')->plainTextToken;

        SetLog::withEvent(LogEvents::REFRESH_TOKEN)
            ->causedBy($request->user())
            ->withProperties([
                'performedOn' => [
                    'class' => Authentication::class,
                    'method' => 'refresh'
                ]
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

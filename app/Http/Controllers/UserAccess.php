<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommenterUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;

/**
 * UserAccess Controller
 *
 * Controller for user access related actions
 */
class UserAccess
{

    /**
     * Get authenticated commenter profile
     *
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"UserAccess"},
     *     summary="Get commenter profile",
     *     security={{{"sanctum":{}}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful fetch commenter data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil memuat data"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="01982faf-bf2e-7050-8c0a-cdef6db19460"),
     *                 @OA\Property(property="name", type="string", example="andriana"),
     *                 @OA\Property(property="avatar_url", type="string", example=""),
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
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function me(): JsonResponse
    {
        SetLog::withEvent(LogEvents::FETCHING_COMMENTER)
            ->causedBy(auth()->guard()->user())
            ->performedOn(auth()->guard()->user())
            ->withProperties([
                'performedOn' => [
                    'class' => UserAccess::class,
                    'method' => 'me'
                ]
            ])
            ->withMessage("Fetching commenter from guard - sanctum")
            ->build();

        $data = Fractal::useCommenterTransformer(auth()->guard()->user())
            ->withIncludes([
                'details',
                'statistics',
                'comments'
            ])
            ->buildWithArraySerializer();

        return response()->json([
            'success' => true,
            'message' => "Berhasil memuat data",
            'data' => $data
        ]);
    }

    /**
     * Update authenticated commenter profile
     *
     * @OA\Put(
     *     path="/api/auth/profile",
     *     tags={"UserAccess"},
     *     summary="Update commenter profile",
     *     security={{{"sanctum":{}}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="bio", type="string", example="A short bio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful update",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil update data"),
     *             @OA\Property(property="data", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update profile",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal menyimpan data, silahkan coba kembali")
     *         )
     *     )
     * )
     *
     * @param CommenterUpdateRequest $request The incoming request containing profile data
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function profile(CommenterUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        SetLog::withEvent(LogEvents::UPDATE)
            ->causedBy(auth()->guard()->user())
            ->performedOn(auth()->guard()->user())
            ->withProperties([
                'performedOn' => [
                    'class' => UserAccess::class,
                    'method' => 'profile'
                ]
            ])
            ->withMessage("Prepare to update profile commenter")
            ->build();

        $commenter = AuthDo::findCommenterById(auth()->guard()->user()->id);
        $updatedData = AuthDo::updateCommenter($commenter, $validated);

        Fractal::useCommenterTransformer($commenter)
            ->withIncludes([
                'details',
            ])
            ->buildWithArraySerializer();

        SetLog::withEvent(LogEvents::UPDATE)
            ->causedBy($commenter)
            ->performedOn($commenter)
            ->withProperties([
                'performedOn' => [
                    'class' => UserAccess::class,
                    'method' => 'profile'
                ]
            ])
            ->withMessage("Profile commenter updated successfully")
            ->build();

        return response()->json([
            'success' => true,
            'message' => "Berhasil update data",
            'data' => $updatedData
        ]);
    }

    /**
     * Delete authenticated commenter account
     *
     * @OA\Delete(
     *     path="/api/auth/account",
     *     tags={"UserAccess"},
     *     summary="Delete commenter account",
     *     security={{{"sanctum":{}}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful deletion",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil menghapus akun")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete account",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal menghapus akun, silahkan coba kembali")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function deleteAccount(): JsonResponse
    {
        SetLog::withEvent(LogEvents::DELETE)
            ->causedBy(auth()->guard()->user())
            ->performedOn(auth()->guard()->user())
            ->withProperties([
                'performedOn' => [
                    'class' => UserAccess::class,
                    'method' => 'deleteAccount'
                ]
            ])
            ->withMessage("Prepare to delete account commenter")
            ->build();

        $commenter = AuthDo::findCommenterById(auth()->guard()->user()->id);
        AuthDo::deleteCommenter($commenter);

        SetLog::withEvent(LogEvents::DELETE)
            ->causedBy($commenter)
            ->performedOn($commenter)
            ->withProperties([
                'performedOn' => [
                    'class' => UserAccess::class,
                    'method' => 'deleteAccount'
                ]
            ])
            ->withMessage("Account commenter deleted successfully")
            ->build();

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus akun"
        ]);
    }
}

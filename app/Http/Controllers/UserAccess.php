<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommenterUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * UserAccess Controller
 *
 * Controller for user access related actions
 */
class UserAccess
{

    /**
     * Get authenticated user's profile information
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
     * Update authenticated user's profile
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
     * Delete authenticated user's account
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function deleteAccount()
    {
        SetLog::withEvent(DoEvents::DELETE)
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

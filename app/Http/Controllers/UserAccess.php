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
     *
     * @response {
     *   "success": true,
     *   "message": "Berhasil memuat data",
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "details": {},
     *     "statistics": {},
     *     "comments": []
     *   }
     * }
     */
    public function me(): JsonResponse
    {
        SetLog::withEvent(LogEvents::FETCHING_COMMENTER)
            ->causedBy(Arr::only((array)auth()->guard()->user(), ['name', 'email']))
            ->performedOn(UserAccess::class)
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
     *
     * @response {
     *   "success": true,
     *   "message": "Berhasil update data",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Name",
     *     "email": "updated@example.com",
     *     "details": {}
     *   }
     * }
     */
    public function profile(CommenterUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        SetLog::withEvent(LogEvents::STORING)
            ->causedBy(Arr::only((array)auth()->guard()->user(), ['name', 'email']))
            ->performedOn(UserAccess::class)
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
            ->causedBy(Arr::only((array)$commenter, ['name', 'email']))
            ->performedOn(UserAccess::class)
            ->withMessage("Update data profile commenter success")
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
     *
     * @response {
     *   "success": true,
     *   "message": "Account deleted successfully"
     * }
     */
    public function deleteAccount()
    {

    }
}

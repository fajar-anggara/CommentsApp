<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommenterUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class UserAccess
{

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
     * @Method PUT
     * @return JsonResponse
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

    public function deleteAccount()
    {

    }
}

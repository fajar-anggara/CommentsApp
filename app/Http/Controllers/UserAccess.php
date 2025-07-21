<?php

namespace App\Http\Controllers;

use App\Enums\Badges;
use App\Enums\LogEvents;
use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Models\StatisticUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function profile()
    {

    }

    public function deleteAccount()
    {

    }
}

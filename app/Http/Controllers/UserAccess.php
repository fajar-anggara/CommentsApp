<?php

namespace App\Http\Controllers;

use App\Facades\AuthDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserAccess
{

    public function me()
    {
//        dd(auth()->guard()->user()->name);
        $data = Fractal::useCommenterTransformer(auth()->guard()->user())
            ->withIncludes([
                'details',
                'statistics'
            ])
            ->buildWithArraySerializer();

        SetLog::withEvent('Fetching Commenter')
            ->causedBy(Arr::only((array)auth()->guard()->user(), ['name', 'email']))
            ->performedOn(UserAccess::class)
            ->withMessage("Prepare to fetch user")
            ->build();

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

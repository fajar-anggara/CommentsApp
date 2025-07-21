<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/testing', function () {
    return view('welcome');
})->middleware('auth:sanctum');

// Public Activity Log UI
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

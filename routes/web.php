<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ApiSpecController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/testing', function () {
    return view('welcome');
})->middleware('auth:sanctum');

// Public Activity Log UI
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

// API Docs UI
Route::get('/api-specs', [ApiSpecController::class, 'index'])->name('api-specs.index');
Route::get('/api-specs/{spec}.json', [ApiSpecController::class, 'json'])->name('api-specs.json');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('school-classes', \App\Http\Controllers\SchoolClassController::class);
    Route::apiResource('students', \App\Http\Controllers\StudentController::class);
    Route::patch('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::delete('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'destroy']);

    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'create']);
    Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store']);
    Route::get('/attendance/recap', [\App\Http\Controllers\AttendanceReportController::class, 'index']);
});

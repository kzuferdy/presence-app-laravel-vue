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

    // Dashboard - Accessible by all roles
    Route::middleware(['role:admin,principal,teacher'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);
        Route::patch('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::put('/password', [\App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);
        Route::delete('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'destroy']);
    });

    // Attendance - Create/Store (Admin & Teacher)
    Route::middleware(['role:admin,teacher'])->group(function () {
        Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'create']);
        Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store']);
    });

    // Attendance Recap - (Admin, Principal, Teacher?) 
    // User said Teacher: "only see menu attendance and dashboard". 
    // Principal: "read only master data". 
    // Usually Principal needs to see reports. Admin too.
    Route::middleware(['role:admin,principal,teacher'])->group(function () {
         Route::get('/attendance/recap', [\App\Http\Controllers\AttendanceReportController::class, 'index']);
    });

    // Master Data - Read Only (Admin & Principal & Teacher)
    // Teacher needs read access for Attendance features (selecting class/year, viewing student list)
    Route::middleware(['role:admin,principal,teacher'])->group(function () {
        Route::apiResource('students', \App\Http\Controllers\StudentController::class)->only(['index', 'show']);
        Route::apiResource('school-classes', \App\Http\Controllers\SchoolClassController::class)->only(['index', 'show']);
        Route::apiResource('academic-years', \App\Http\Controllers\AcademicYearController::class)->only(['index', 'show']);
        Route::apiResource('subjects', \App\Http\Controllers\SubjectController::class)->only(['index', 'show']);
        Route::apiResource('schedules', \App\Http\Controllers\ScheduleController::class)->only(['index', 'show']);
    });

    // Master Data - Full Access (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('students', \App\Http\Controllers\StudentController::class)->except(['index', 'show']);
        Route::apiResource('school-classes', \App\Http\Controllers\SchoolClassController::class)->except(['index', 'show']);
        Route::apiResource('academic-years', \App\Http\Controllers\AcademicYearController::class)->except(['index', 'show']);
        
        Route::apiResource('teachers', \App\Http\Controllers\TeacherController::class);
        Route::apiResource('subjects', \App\Http\Controllers\SubjectController::class)->except(['index', 'show']);
        Route::apiResource('schedules', \App\Http\Controllers\ScheduleController::class)->except(['index', 'show']);
    });
});


<?php

use App\Modules\Attendance\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('attendance')
    ->name('attendance.')
    ->group(function () {

        Route::get('/', function () {
            return inertia('Attendance/Index');
        })->name('index');

        Route::get('/students', [StudentController::class, 'index'])
            ->name('students.index');

        Route::get('/students/create', [StudentController::class, 'create'])
            ->name('students.create');

        Route::post('/students', [StudentController::class, 'store'])
            ->name('students.store');
    });

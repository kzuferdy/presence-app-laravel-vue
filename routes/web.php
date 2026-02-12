<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('attendance/students', \App\Http\Controllers\StudentController::class);
    Route::get('attendance/presence', [\App\Http\Controllers\AttendanceController::class, 'create'])->name('attendance.presence');
    Route::post('attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/recap', [\App\Http\Controllers\AttendanceReportController::class, 'index'])->name('attendance.recap');
    
    Route::resource('master/classes', \App\Http\Controllers\SchoolClassController::class)->except(['create', 'edit', 'show']);
});

require __DIR__.'/settings.php';

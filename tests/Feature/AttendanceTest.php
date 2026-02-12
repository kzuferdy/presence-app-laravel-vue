<?php

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('students table exists', function () {
    expect(Schema::hasTable('students'))->toBeTrue();
});

test('attendances table exists', function () {
    expect(Schema::hasTable('attendances'))->toBeTrue();
});

test('can create student', function () {
    $student = Student::create([
        'name' => 'Test Student',
        'nis' => '1234567890',
        'class' => 'X RPL 1'
    ]);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->name)->toBe('Test Student');
});

test('can create attendance for student', function () {
    $student = Student::firstOrCreate([
        'nis' => '1234567890'
    ], [
        'name' => 'Test Student',
        'class' => 'X RPL 1'
    ]);

    $attendance = Attendance::create([
        'student_id' => $student->id,
        'date' => now(),
        'status' => 'present'
    ]);

    expect($attendance)->toBeInstanceOf(Attendance::class);
    expect($attendance->student_id)->toBe($student->id);
    expect($attendance->status)->toBe('present');
    expect($student->attendances)->toHaveCount(1);
});

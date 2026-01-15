<?php

// app/Modules/Attendance/Http/Controllers/StudentController.php
namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return inertia('Attendance/Index', [
            'students' => Student::latest()->get(),
        ]);
    }

    public function create()
    {
        return inertia('Attendance/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'nis' => 'required|unique:students',
            'class' => 'required|string',
        ]);

        Student::create($data);

        return redirect()->route('attendance.students.index');
    }
}

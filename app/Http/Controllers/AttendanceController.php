<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create()
    {
        $students = \App\Models\Student::with(['attendances' => function ($query) {
            $query->whereDate('date', now());
        }])->orderBy('name')->get();

        return response()->json([
            'students' => $students,
            'date' => now()->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,sick,permission,alpha',
        ]);

        Attendance::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'date' => $request->date,
            ],
            [
                'status' => $request->status
            ]
        );

        return response()->json(['message' => 'Attendance recorded'], 200);
    }
}

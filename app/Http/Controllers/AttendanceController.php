<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        
        $query = \App\Models\Student::query();

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        $students = $query->with(['attendances' => function ($query) use ($date, $subjectId) {
            $query->whereDate('date', $date);
            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }
        }])->orderBy('name')->get();

        return response()->json([
            'students' => $students,
            'date' => $date,
            'class_id' => $classId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'status' => 'required|in:present,sick,permission,alpha',
        ]);

        // Optional: Check if schedule exists (user request: "Validasi absensi")
        // We can implement strict checking here or just rely on frontend warning.
        // For now, we proceed with saving.

        Attendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'subject_id' => $validated['subject_id'],
                'date' => $validated['date'],
            ],
            [
                'user_id' => $request->user()->id,
                'status' => $validated['status']
            ]
        );

        return response()->json(['message' => 'Attendance recorded'], 200);
    }
}

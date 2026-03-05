<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $totalClasses = SchoolClass::count();
        $totalStudents = Student::count();

        $quotes = [
            "Education is the most powerful weapon which you can use to change the world. – Nelson Mandela",
            "The roots of education are bitter, but the fruit is sweet. – Aristotle",
            "Investment in knowledge pays the best interest. – Benjamin Franklin",
            "Education is not preparation for life; education is life itself. – John Dewey",
            "The beautiful thing about learning is that no one can take it away from you. – B.B. King"
        ];

        return response()->json([
            'active_academic_year' => $activeYear ? $activeYear->name : 'No Active Year',
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'quote' => $quotes[array_rand($quotes)],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $students = Student::with(['attendances' => function ($query) use ($month, $year) {
            $query->whereMonth('date', $month)
                  ->whereYear('date', $year);
        }])->orderBy('name')->get();

        return response()->json([
            'students' => $students,
            'filters' => [
                'month' => (int)$month,
                'year' => (int)$year,
            ]
        ]);
    }
}

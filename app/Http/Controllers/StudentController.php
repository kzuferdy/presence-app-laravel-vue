<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('schoolClass')->latest()->get();
        return Inertia::render('Attendance/Students/Index', [
            'students' => $students
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Attendance/Students/FormCreate', [
            'classes' => SchoolClass::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->school_class_id) {
            $schoolClass = SchoolClass::find($request->school_class_id);
            if ($schoolClass) {
                $request->merge(['class' => $schoolClass->name]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students,nis',
            'class' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'address' => 'nullable|string',
            'school_class_id' => 'nullable|exists:school_classes,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        Student::create($data);

        return redirect()->route('students.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return Inertia::render('Attendance/Students/FormEdit', [
            'student' => $student,
            'classes' => SchoolClass::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        if ($request->school_class_id) {
            $schoolClass = SchoolClass::find($request->school_class_id);
            if ($schoolClass) {
                $request->merge(['class' => $schoolClass->name]);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'class' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'address' => 'nullable|string',
            'school_class_id' => 'nullable|exists:school_classes,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()->route('students.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }
        $student->delete();
        return redirect()->route('students.index');
    }
}

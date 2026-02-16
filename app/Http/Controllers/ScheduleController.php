<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Schedule::with(['class', 'subject', 'teacher']);

        if ($request->has('school_class_id')) {
            $query->where('school_class_id', $request->school_class_id);
        }
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        return response()->json($query->orderBy('day_of_week')->orderBy('start_time')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id', // Teacher
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule = \App\Models\Schedule::create($validated);

        return response()->json($schedule->load(['class', 'subject', 'teacher']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = \App\Models\Schedule::with(['class', 'subject', 'teacher'])->findOrFail($id);
        return response()->json($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = \App\Models\Schedule::findOrFail($id);

        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'user_id' => 'required|exists:users,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($validated);

        return response()->json($schedule->load(['class', 'subject', 'teacher']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = \App\Models\Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json(null, 204);
    }
}

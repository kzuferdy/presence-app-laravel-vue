<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = \App\Models\User::where('role', 'teacher')->with('classes')->get();
        return response()->json($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nip' => 'nullable|string|unique:users',
            'mapel' => 'nullable|string',
            'classes' => 'array',
            'classes.*' => 'exists:school_classes,id',
        ]);

        $validated['role'] = 'teacher';
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        $teacher = \App\Models\User::create($validated);

        if (isset($validated['classes'])) {
            $teacher->classes()->sync($validated['classes']);
        }

        return response()->json($teacher->load('classes'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = \App\Models\User::where('role', 'teacher')->with('classes')->findOrFail($id);
        return response()->json($teacher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = \App\Models\User::where('role', 'teacher')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $teacher->id,
            'password' => 'nullable|string|min:8',
            'nip' => 'nullable|string|unique:users,nip,' . $teacher->id,
            'mapel' => 'nullable|string',
            'classes' => 'array',
            'classes.*' => 'exists:school_classes,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $teacher->update($validated);

        if (isset($validated['classes'])) {
            $teacher->classes()->sync($validated['classes']);
        }

        return response()->json($teacher->load('classes'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = \App\Models\User::where('role', 'teacher')->findOrFail($id);
        $teacher->delete();

        return response()->json(null, 204);
    }
}

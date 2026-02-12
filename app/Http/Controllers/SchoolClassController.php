<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = SchoolClass::latest()->get();
        return response()->json($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name',
        ]);

        $class = SchoolClass::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($class, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:school_classes,name,' . $class->id,
        ]);

        $class->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($class);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return response()->json(null, 204);
    }
}

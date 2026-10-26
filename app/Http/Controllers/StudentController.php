<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Student retrieved successfully',
            'data' =>Student::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:students',
            'course' => 'required|string',
        ]);

        $student = Student::create($data);

        return response()->json([
            'message' => 'Student created successfully',
            'data' => $student,
        ], 201);
    }

    public function show(Student $student)
    {
        return response()->json([
            'message' => 'Student retrieved successfully',
            'data' => $student,
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'student_id' => 'required|string|unique:students,student_id' . $student->id,
            'fullname' => 'required|string',
            'email' => 'required|email|unique:students,email' . $student->id,
            'course' => 'required|string',
        ]);

        $student->update($data);

        return response()->json([
            'message' => 'Student updated successfully',
            'data' => $student,
        ]);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully',
        ]);
    }
}

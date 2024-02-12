<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function Index(Request $request)
    {

        $students = Student::get();

        return Inertia::render('Students/Index', [
            'students' => $students,
        ]);
    }
}
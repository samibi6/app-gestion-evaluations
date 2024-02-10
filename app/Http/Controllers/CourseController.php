<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStoreRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {

        $courses = Course::get();

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
        ]);
    }
    
    public function store(CourseStoreRequest $request)
    {
        $course = Course::make();
        $course->name = $request->validated()['name'];
        $course->code = $request->validated()['code'];
        
 

        $course->save();
        $request->session()->flash('flash.banner', 'Le cours a bien été créé.');
        return redirect()->back();
    }
}
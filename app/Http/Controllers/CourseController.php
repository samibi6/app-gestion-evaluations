<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStoreRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseUser;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
{

    //faudrait combiner ces queries de manière efficace, et peut-être même combiner les props ?
    $courses = Course::get();
    
    $users = User::get();
    
    $sections = Section::get();
    
    $sectionsByCourses = Course::join('course_sections', 'courses.id', '=', 'course_sections.course_id')
        ->join('sections', 'course_sections.section_id', '=', 'sections.id')
        ->select('courses.id as course-id', 'sections.*', 'course_sections.year') //le groupBy ne fonctionne pas correctement sans l'alias, même en précisant courses.id
        ->get()
        ->groupBy('course-id');

    $usersByCourses = Course::join('course_users', 'courses.id', '=', 'course_users.course_id')
        ->join('users', 'course_users.user_id', '=', 'users.id')
        ->select('courses.id as course-id', 'users.*') 
        ->get()
        ->groupBy('course-id');

    return Inertia::render('Courses/Index', [
        'courses' => $courses,
        'users' => $users,
        'sections' => $sections,
        'sectionsByCourses' => $sectionsByCourses,
        'usersByCourses' => $usersByCourses,
    ]);
}

    public function store(CourseStoreRequest $request)
{
  
    $course = Course::make();
    $course->name = $request->validated()['name'];
    $course->code = $request->validated()['code'];

    $course->save();


    $courseSection = CourseSection::make();
    $courseSection->course_id = $course->id; //pour lier la section choisie avec le cours qu'on crée
    $courseSection->section_id = $request->validated()['section'];
    $courseSection->year = $request->validated()['year'];
    
    $courseSection->save();


    $courseUser = CourseUser::make();
    $courseUser->course_id = $course->id; //pour lier le prof choisi avec le cours qu'on crée
    $courseUser->user_id = $request->validated()['user']; 
    
    $courseUser->save();


    $request->session()->flash('flash.banner', 'Le cours a bien été créé.');
    return redirect()->back();
}

}


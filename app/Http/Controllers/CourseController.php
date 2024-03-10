<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStoreRequest;
use App\Http\Requests\CourseUpdateRequest;
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
        ->select('courses.id as course-id', 'sections.*') //le groupBy ne fonctionne pas correctement sans l'alias, même en précisant courses.id
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
    $course->lead = $request->validated()['lead'];
    $course->is_tfe = $request->validated()['is_tfe'];

    $course->save();


    $courseSection = CourseSection::make();
    $courseSection->course_id = $course->id; //pour lier la section choisie avec le cours qu'on crée
    $courseSection->section_id = $request->validated()['section'];
    
    $courseSection->save();


    $courseUser = CourseUser::make();
    $courseUser->course_id = $course->id; //pour lier le prof choisi avec le cours qu'on crée
    $courseUser->user_id = $request->validated()['user']; 
    
    $courseUser->save();


    $request->session()->flash('flash.banner', 'Le cours a bien été créé.');
    return redirect()->back();
}

public function edit(Course $course){
    
    $sections = Section::get();
    $users = User::get();
    
    $sectionsByCurrentCourse = Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
        ->where('course_sections.course_id', $course->id)
        ->select('sections.*')
        ->get();

    $usersByCurrentCourse = User::join('course_users', 'users.id', '=', 'course_users.user_id')
        ->where('course_users.course_id', $course->id)
        ->select('users.*')
        ->get();

    return Inertia::render('Courses/Edit', [
        'course' => $course,
        'sections' => $sections,
        'users' => $users,
        'sectionsByCurrentCourse' => $sectionsByCurrentCourse,
        'usersByCurrentCourse' => $usersByCurrentCourse,
    ]);
}

    
   
    public function update(CourseUpdateRequest $request, Course $course)
    {

        $course->update([
            'name' => $request->validated()['name'],
            'code' => $request->validated()['code'],
            'lead' => $request->validated()['lead'],
            'is_tfe' => $request->validated()['is_tfe'],
            
        ]);
    
        //bon là faudrait ajouter quelque chose qui check dans le cas où un cours n'aurait pas d'année, de section ou de chargé de cours (c'est pas censé arriver mais bon avec les seeders si)
      // dd($course->id);
     $courseSection = CourseSection::where('course_id', $course->id)->first();
     if ($courseSection) {
         $courseSection->update([
             'section_id' => $request->validated()['section'],
             
         ]);
     } else {
         
         $courseSection = CourseSection::make([
             'course_id' => $course->id,
             'section_id' => $request->validated()['section'],
            
         ]);
         $courseSection->save();
     }
        
        $courseUser = CourseUser::where('course_id', $course->id)->first(); //faudra revoir la façon dont on store les sections et users, faudra ptêtre des champs différents dans la db genre 'user1' 'user2' etc en fonction du nombre de options select, ou travailler avec un array, à voir...
        if($courseUser) {
            $courseUser->update([
                'user_id' => $request->validated()['user'],
        ]);
        
        } else {
        $courseUser = CourseUser::make([
            'course_id' => $course->id,
            'user_id' => $request->validated()['user'],
        ]);
        $courseUser->save();
}
    
        $request->session()->flash('flash.banner', 'Le cours a bien été modifié.');
        return redirect()->route('courses.index');

    }

    public function delete(Course $course)
    {
 //dd($course);
          $course->delete();
  
          session()->flash('flash.banner', 'Le cours a bien été supprimé.');
        
          return redirect()->route('courses.index');
    }


}


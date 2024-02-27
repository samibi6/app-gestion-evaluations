<?php

namespace App\Http\Controllers;

use App\Http\Requests\AptitudeStoreRequest;
use App\Http\Requests\AptitudeUpdateRequest;
use App\Http\Requests\CriteriaStoreRequest;
use App\Http\Requests\CriteriaUpdateRequest;
use App\Models\Aptitude;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseUser;
use App\Models\Criteria;
use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AptitudeController extends Controller
{
    public function index()
    {

    //faudrait combiner ces queries de manière efficace, et peut-être même combiner les props ?
    $courses = Course::get();
    $sections = Section::get();
    $aptitudes = Aptitude::get();
    $coursesBySections = Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
        ->join('courses', 'courses.id', '=', 'course_sections.course_id')
        ->select('sections.id as section-id', 'courses.*')
        ->get()
        ->groupBy('section-id');
        
        $courses = Course::get();
        $aptitudesByCourses = [];
        
        foreach ($courses as $course) {
            $aptitudesByCourses[$course->id] = $course->aptitudes()->get();
        }

        foreach ($aptitudes as $aptitude) {
            $criteriasByAptitudes[$aptitude->id] = $aptitude->criterias()->get();
            
        }
        
        
   

    return Inertia::render('Aptitudes/Index', [
        'sections' => $sections,
        'courses' => $courses,
        'coursesBySections' => $coursesBySections,
        'aptitudesByCourses' =>  $aptitudesByCourses,
        'criteriasByAptitudes' => $criteriasByAptitudes,
    ]);
}


    public function storeAptitude(AptitudeStoreRequest $request)
    {    
        
        $aptitude = Aptitude::make([
            'description' => $request->validated()['aptitude_description'],
            'course_id' => $request->validated()['course']
        ]);
       
        $aptitude->save();
        return redirect()->back();
    }

    public function storeCriteria(CriteriaStoreRequest $request)
     {
        $criteria = Criteria::make([
            'description' => $request->validated()['criteria_description'],
            'aptitude_id' => $request->validated()['aptitude'],
        ]);
        $criteria->save();
        return redirect()->back();
     }

     public function updateAptitude(AptitudeUpdateRequest $request, Aptitude $aptitude){

        $aptitude->update([
            'description' => $request->validated()['aptitude_description'],        
        ]);

        $request->session()->flash('flash.banner', 'L\'AA a bien été modifié.');
        return redirect()->back();
        
     }
     public function updateCriteria(CriteriaUpdateRequest $request, Criteria $criteria){

        $criteria->update([
            'description' => $request->validated()['criteria_description'],        
        ]);

        $request->session()->flash('flash.banner', 'Le critère a bien été modifié.');
        return redirect()->back();
        
     }

     public function deleteAptitude(Aptitude $aptitude){
    
          $aptitude->delete();
  
           session()->flash('flash.banner', 'L\'AA a bien été supprimé.');
        
          return redirect()->back();
     }
     public function deleteCriteria(Criteria $criteria){
    
          $criteria->delete();
  
          session()->flash('flash.banner', 'Le critère a bien été supprimé.');
        
          return redirect()->back();
     }













 /*   public function store(CourseStoreRequest $request)
{
  
    $course = Course::make();
    $course->name = $request->validated()['name'];
    $course->code = $request->validated()['code'];

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
    }*/

}


<?php

namespace App\Http\Controllers;

use App\Http\Requests\AptitudeStoreRequest;
use App\Http\Requests\AptitudeUpdateRequest;
use App\Http\Requests\CriteriaStoreRequest;
use App\Http\Requests\CriteriaUpdateRequest;
use App\Http\Requests\LeadUpdateRequest;
use App\Http\Requests\ProficiencyStoreRequest;
use App\Http\Requests\ProficiencyUpdateRequest;
use App\Models\Aptitude;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseUser;
use App\Models\Criteria;
use App\Models\Proficiency;
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
    $proficiencies = Proficiency::get();
    
    $coursesBySections = Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
        ->join('courses', 'courses.id', '=', 'course_sections.course_id')
        ->select('sections.id as section-id', 'courses.*')
        ->get()
        ->groupBy('section-id');
        
        $aptitudesByCourses = [];
        
        foreach ($courses as $course) {
            $aptitudesByCourses[$course->id] = $course->aptitudes()->get();
        }

        foreach ($aptitudes as $aptitude) {
            $criteriasByAptitudes[$aptitude->id] = $aptitude->criterias()->get();    
        }
        
        foreach ($courses as $course) {
            $proficienciesByCourses[$course->id] = $course->proficiencies()->get();
        }
        
   

    return Inertia::render('Aptitudes/Index', [
        'sections' => $sections,
        'courses' => $courses,
        'coursesBySections' => $coursesBySections,
        'aptitudesByCourses' =>  $aptitudesByCourses,
        'criteriasByAptitudes' => $criteriasByAptitudes,
        'proficienciesByCourses' => $proficienciesByCourses,
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

     public function storeProficiency(ProficiencyStoreRequest $request)
     {
       // dd($request->validated());
        $proficiency = Proficiency::make([
            'criteria_skill' => $request->validated()['criteria_skill'],
            'indicator' => $request->validated()['indicator'],
            'course_id' => $request->validated()['course']
        ]);
        $proficiency->save();
        return redirect()->back();
     }

     public function updateProficiency(ProficiencyUpdateRequest $request, Proficiency $proficiency){

        $proficiency->update([
            'criteria_skill' => $request->validated()['criteria_skill'],        
            'indicator' => $request->validated()['indicator'],        
        ]);

        $request->session()->flash('flash.banner', 'Le critère de maitrise et son indicateur ont bien été modifiés.');
        return redirect()->back();
        
     }
     public function deleteProficiency(Proficiency $proficiency){
    
          $proficiency->delete();
  
          session()->flash('flash.banner', 'Le critère de maitrise et son indicateur ont bien été supprimés.');
        
          return redirect()->back();
     }

     public function updateLead(LeadUpdateRequest $request,  Course $course)
     {
       //dd($request->validated());
        $course->update([
            'lead' => $request->validated()['lead'],        
        ]);

        $request->session()->flash('flash.banner', 'Le chapeau du cours a bien été modifié');
        return redirect()->back();
     }

}


<?php

namespace App\Http\Controllers;

use App\Models\Aptitude;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Criteria;
use App\Models\CriteriaStudent;
use App\Models\Proficiency;
use App\Models\ProficiencyStudent;
use App\Models\Section;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EvalController extends Controller
{
    public function index()
    {
        $sections = Section::get();
        $courses =
            Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
            ->join('courses', 'courses.id', '=', 'course_sections.course_id')
            ->select('sections.id as section-id', 'courses.*')
            ->get()
            ->groupBy('section-id');
        return Inertia::render('Evals/Index', [
            'courses' => $courses,
            'sections' => $sections,
        ]);
    }

    public function show($courseId, $sectionId)
    {
        // Récupérer le cours donné avec ses aptitudes, critères et proficiencies
        $course = Course::with('aptitudes.criterias', 'proficiencies')
            ->findOrFail($courseId);

        // Récupérer les étudiants inscrits à ce cours
        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $courseId)->where('section_students.section_id', $sectionId)->select('students.*')->orderBy('students.id')
            ->get();

        $dateEval = CourseStudent::where('course_id', $courseId)->pluck('date_eval', 'student_id')->toArray();

        return Inertia::render('Evals/show', [
            'course' => $course,
            'students' => $students,
            'section' => $sectionId,
            'dateEval' => $dateEval,
        ]);
    }

    public function edit($courseId, $studentId, $sectionId)
    {
        $next =
            CourseStudent::orderBy('course_students.student_id')
            ->join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_id', $courseId)
            ->where('section_id', $sectionId)
            ->where('course_students.student_id', '>', $studentId)
            ->select('students.*')
            ->first();

        $criteriaData = Criteria::pluck('description', 'id')->toArray();

        $criteriaByApt = Criteria::get()->groupBy('aptitude_id');

        $aptitudes = Aptitude::where('course_id', $courseId)->get();

        $criteriaStudents = CriteriaStudent::all();
        // where('student_id', $studentId);

        $section = Section::where('id', $sectionId)->first();
        $student = Student::where('id', $studentId)->first();
        $course = Course::where('id', $courseId)->first();

        // Formatage des données pour correspondre à la structure souhaitée
        $acquired = [];
        foreach ($criteriaStudents as $criteriaStudent) {
            $acquired[$criteriaStudent->student_id][$criteriaStudent->criteria_id] = $criteriaStudent->acquired;
        }

        // MANQUE REQUETES POUR PROFICIENCIES struct= aptitude -> criteria -> acquired

        $proficiencies = Proficiency::where('course_id', $courseId)->get();

        $proficiencyStudents = ProficiencyStudent::all();
        $acquiredProf = [];
        foreach ($proficiencyStudents as $pS) {
            $acquiredProf[$pS->student_id][$pS->proficiency_id] = $pS->score;
        }

        return Inertia::render('Evals/edit', [
            'courseId' => $courseId,
            'studentId' => $studentId,
            'sectionId' => $sectionId,
            'next' => $next,
            'criteriaData' => $criteriaData,
            'criteriaByApt' => $criteriaByApt,
            'aptitudes' => $aptitudes,
            'acquired' => $acquired,
            'section' => $section,
            'proficiencies' => $proficiencies,
            'acquiredProf' => $acquiredProf,
            'student' => $student,
            'course' => $course,
        ]);
    }

    public function store(Request $request, Student $studentId, $sectionId, $courseId)
    {
        // dd($studentId->id);
        // $request->criteria;
        // $request->proficiency;
        foreach ($request->criteria as $id => $value) {
            CriteriaStudent::updateOrCreate(
                ['criteria_id' => $id, 'student_id' => $studentId->id],
                ['acquired' => $value]
            );
        }

        foreach ($request->proficiency as $id => $value) {
            ProficiencyStudent::updateOrCreate(
                ['proficiency_id' => $id, 'student_id' => $studentId->id],
                ['score' => $value]
            );
        }

        $dateTime = new DateTime();

        $currentDate = $dateTime->format('Y-m-d H:i:s');

        CourseStudent::updateOrCreate(
            ['course_id' => $courseId, 'student_id' => $studentId->id],
            ['date_eval' => $currentDate]
        );
        // $criteriaStudent
        // $proficiencyStudent
        return redirect()->route('evals.show', ['courseId' => $courseId, 'sectionId' => $sectionId])->with('success', 'évalué avec succès');
    }
}

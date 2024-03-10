<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjournmentStoreRequest;
use App\Http\Requests\DeniedStoreRequest;
use App\Http\Requests\EvalStoreRequest;
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

        $AllCourses = Course::all();
        return Inertia::render('Evals/Index', [
            'courses' => $courses,
            'sections' => $sections,
            'AllCourses' => $AllCourses,
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

        $sectionData = Section::where('id', $sectionId)->first();

        return Inertia::render('Evals/show', [
            'course' => $course,
            'students' => $students,
            'section' => $sectionId,
            'dateEval' => $dateEval,
            'sectionData' => $sectionData,
        ]);
    }

    public function edit($courseId, $studentId, $sectionId)
    {
        // $next =
        //     CourseStudent::orderBy('course_students.student_id')
        //     ->join('students', 'students.id', "=", "course_students.student_id")
        //     ->join('section_students', 'section_students.student_id', '=', 'students.id')
        //     ->where('course_id', $courseId)
        //     ->where('section_id', $sectionId)
        //     ->where('course_students.student_id', '>', $studentId)
        //     ->select('students.*')
        //     ->first();
        $courseStudentData = CourseStudent::where('course_students.course_id', $courseId)->where('course_students.student_id', $studentId)->first();

        $criteriaData = Criteria::pluck('description', 'id')->toArray();

        $criteriaByApt = Criteria::get()->groupBy('aptitude_id');

        $aptitudes = Aptitude::where('course_id', $courseId)->get();

        $criteriaStudents = CriteriaStudent::all();
        // where('student_id', $studentId);

        $student = Student::where('id', $studentId)->first();
        $course = Course::where('id', $courseId)->first();

        // Formatage des données pour correspondre à la structure souhaitée
        $acquired = [];
        foreach ($criteriaStudents as $criteriaStudent) {
            foreach ($aptitudes as $aptitude) {
                if (isset($criteriaByApt[$aptitude->id])) {
                    foreach ($criteriaByApt[$aptitude->id] as $criteria) {
                        if (isset($criteriaStudent->criteria_id, $criteria->id)) {
                            if ($criteriaStudent->criteria_id === $criteria->id) {
                                if (isset($criteriaStudent->acquired)) {
                                    $acquired[$criteriaStudent->student_id][$criteriaStudent->criteria_id] = $criteriaStudent->acquired;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // MANQUE REQUETES POUR PROFICIENCIES struct= aptitude -> criteria -> acquired

        $proficiencies = Proficiency::where('course_id', $courseId)->get();

        $proficiencyStudents = ProficiencyStudent::all();
        $acquiredProf = [];
        foreach ($proficiencyStudents as $pS) {
            foreach ($proficiencies as $proficiency) {
                if ($pS->proficiency_id === $proficiency->id) {
                    $acquiredProf[$pS->student_id][$pS->proficiency_id] = $pS->score;
                }
            }
        }
        $sectionData = Section::where('id', $sectionId)->first();
        
        return Inertia::render('Evals/edit', [
            'courseId' => $courseId,
            'studentId' => $studentId,
            'sectionId' => $sectionId,
            // 'next' => $next,
            'criteriaData' => $criteriaData,
            'criteriaByApt' => $criteriaByApt,
            'aptitudes' => $aptitudes,
            'acquired' => $acquired,
            'proficiencies' => $proficiencies,
            'acquiredProf' => $acquiredProf,
            'student' => $student,
            'course' => $course,
            'courseStudentData' => $courseStudentData,
            'sectionData' => $sectionData,
        ]);
    }

    public function store(EvalStoreRequest $request, Student $studentId, $courseId, $sectionId)
    {

        // Iterate through criteria to check if all are equal to 1
        $allCriteriaChecked = true;
        foreach ($request->criteria as $value) {
            if ($value != 1) {
                $allCriteriaChecked = false;
                break;
            }
        }

        // Flag to indicate whether to redirect to failure route at the end
        $redirectAfterProcessing = !$allCriteriaChecked;

        foreach ($request->criteria as $id => $value) {
            CriteriaStudent::updateOrCreate(
                ['criteria_id' => $id, 'student_id' => $studentId->id],
                ['acquired' => $value]
            );
        }



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

        
        if ($redirectAfterProcessing) {
            return redirect()->route('evals.fail', [
                'courseId' => $courseId,
                'sectionId' => $sectionId,
                'studentId' => $studentId->id,
            ]);
        }

        return redirect()->route('evals.show', ['courseId' => $courseId, 'sectionId' => $sectionId])->with('success', 'évalué avec succès');
    }

    public function fail($courseId, $sectionId, $studentId)
    {

        $section = Section::where('id', $sectionId)->first();
        $student = Student::where('id', $studentId)->first();
        $course = Course::where('id', $courseId)->first();

        return Inertia::render('Evals/Fail', [
            'student' => $student,
            'section' => $section,
            'course' => $course,
        ]);
    }
    public function adjournment($courseId, $sectionId, $studentId)
    {

        $section = Section::where('id', $sectionId)->first();
        $student = Student::where('id', $studentId)->first();
        $course = Course::where('id', $courseId)->first();
        $courseStudent = CourseStudent::where('course_id', $courseId)->where('student_id', $studentId)->firstOrFail();

        return Inertia::render('Evals/Adjournment', [
            'student' => $student,
            'section' => $section,
            'course' => $course,
            'courseStudent' => $courseStudent,
        ]);
    }
    public function storeAdjournment(AdjournmentStoreRequest $request, $courseId, $sectionId, $studentId)
    {

        $courseStudent = CourseStudent::where('student_id', $studentId)->where('course_id', $courseId)->firstOrFail();

        $dateTime = new DateTime();
        $currentDate = $dateTime->format('Y-m-d H:i:s');

        $courseStudent->update([
            'date_adjourned' => $currentDate,
            'is_determining' => $request->validated()['is_determining'],
            'is_other' => $request->validated()['is_other'],

            'adjournment_exam_date' => $request->validated()['adjournment_exam_date'],

            'adjournment_blunder_1' => $request->validated()['adjournment_blunder_1'],
            'adjournment_blunder_1_justification' => $request->validated()['adjournment_blunder_1_justification'],

            'adjournment_blunder_2' => $request->validated()['adjournment_blunder_2'],
        ]);



        // return Inertia::render('Evals/Show -> page show du bon cours (pas moyen de mettre la méthode show du controller ici? sinon redéclarer variables nécessaires)', [
        //     'student' => $student,
        //     'section' => $section,
        //     'course' => $course,
        // ]);

        return redirect()->route('evals.show', ['courseId' => $courseId, 'sectionId' => $sectionId]);
    }

    public function denied($courseId, $sectionId, $studentId)
    {
        $section = Section::where('id', $sectionId)->first();
        $student = Student::where('id', $studentId)->first();
        $course = Course::where('id', $courseId)->first();
        $courseStudent = CourseStudent::where('course_id', $courseId)->where('student_id', $studentId)->firstOrFail();

        return Inertia::render('Evals/Denied', [
            'student' => $student,
            'section' => $section,
            'course' => $course,
            'courseStudent' => $courseStudent,
        ]);
    }

    public function storeDenied(DeniedStoreRequest $request, $courseId, $sectionId, $studentId)
    {

        $courseStudent = CourseStudent::where('student_id', $studentId)->where('course_id', $courseId)->firstOrFail();

        $dateTime = new DateTime();
        $currentDate = $dateTime->format('Y-m-d H:i:s');
        $courseStudent->update([
            'date_denied' => $currentDate,

            'is_determining' => $request->validated()['is_determining'],
            'is_other' => $request->validated()['is_other'],

            'denied_exam_date' => $request->validated()['denied_exam_date'],

            'denied_blunder_1' => $request->validated()['denied_blunder_1'],
            'denied_blunder_1_justification' => $request->validated()['denied_blunder_1_justification'],

            'denied_blunder_2' => $request->validated()['denied_blunder_2'],
            'denied_blunder_2_justification' => $request->validated()['denied_blunder_2_justification'],

            'denied_blunder_3' => $request->validated()['denied_blunder_3'],

            'denied_blunder_4' => $request->validated()['denied_blunder_4'],

            'denied_blunder_5' => $request->validated()['denied_blunder_5'],

            'denied_justification_global' => $request->validated()['denied_justification_global'],
        ]);

        return redirect()->route('evals.show', ['courseId' => $courseId, 'sectionId' => $sectionId]);
    }
}

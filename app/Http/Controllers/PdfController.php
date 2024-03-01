<?php

namespace App\Http\Controllers;

use App\Models\Aptitude;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseUser;
use App\Models\Criteria;
use App\Models\CriteriaStudent;
use App\Models\Proficiency;
use App\Models\ProficiencyStudent;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function generateSuccessPdf(Course $course, Section $section)
    {
        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $course->id)->where('section_students.section_id', $section->id)->select('students.*')->orderBy('students.id')
            ->get();

        $student = Student::where('id', 1)->first();

        $criteriaData = Criteria::pluck('description', 'id')->toArray();

        $criteriaByApt = Criteria::get()->groupBy('aptitude_id');

        $aptitudes = Aptitude::where('course_id', $course->id)->get();

        $criteriaStudents = CriteriaStudent::all();

        $courseUser = CourseUser::join('users', 'users.id', '=', "course_users.user_id")->where('course_id', $course->id)->select('users.*')->get();

        $acquired = [];
        foreach ($criteriaStudents as $criteriaStudent) {
            $acquired[$criteriaStudent->student_id][$criteriaStudent->criteria_id] = $criteriaStudent->acquired;
        }

        $proficiencies = Proficiency::where('course_id', $course->id)->get();

        $proficiencyStudents = ProficiencyStudent::all();
        $acquiredProf = [];
        foreach ($proficiencyStudents as $pS) {
            $acquiredProf[$pS->student_id][$pS->proficiency_id] = $pS->score;
        }

        $currentMonth = date('n');

        $currentYear = date('Y');

        if ($currentMonth >= 9) {
            $schoolYear = $currentYear . '-' . ($currentYear + 1);
        } else {
            $schoolYear = ($currentYear - 1) . '-' . $currentYear;
        }

        $data = [
            'student' => $student,
            'section' => $section,
            'courseUser' => $courseUser,
            'schoolYear' => $schoolYear,
            'course' => $course,
            'criteriaData' => $criteriaData,
            'criteriaByApt' => $criteriaByApt,
            'aptitudes' => $aptitudes,
            'acquired' => $acquired[1],
            'proficiencies' => $proficiencies,
            'acquiredProf' => $acquiredProf[1],
        ];

        $pdf = PDF::loadView('pdf.success.view', $data);
        return $pdf->stream('document.pdf');

        // foreach ($students as $student) {
        //     $data = [
        //         'student' => $student,
        //         'section' => $section,
        //         'courseUser' => $courseUser,
        //         'schoolYear' => $schoolYear,
        //         'course' => $course,
        //         'criteriaData' => $criteriaData,
        //         'criteriaByApt' => $criteriaByApt,
        //         'aptitudes' => $aptitudes,
        //         'acquired' => $acquired[$student->id],
        //         'proficiencies' => $proficiencies,
        //         'acquiredProf' => $acquiredProf[$student->id],
        //     ];
        //     $pdf = PDF::loadView('pdf.success.view', $data);
        //     $pdf->stream('document.pdf');
        // }

        // $pdf = PDF::loadView('pdf.success.view', $data);
        // return $pdf->download('document.pdf');
        // return view('pdf.success.view', $data);
        // return $pdf->stream('document.pdf');
    }

    public function generatePostponementPdf($course, $section)
    {
    }

    public function generateFailurePdf($course, $section)
    {
    }
}

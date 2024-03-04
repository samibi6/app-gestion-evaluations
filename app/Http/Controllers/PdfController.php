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
    public function generateSuccessPdf(Course $course, Section $section, Student $student = null)
    {
        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $course->id)->where('section_students.section_id', $section->id)->select('students.*')->orderBy('students.id')
            ->get();

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

        if ($student === null) {
            foreach ($students as $studnt) {
                $data = [
                    'student' => $studnt,
                    'section' => $section,
                    'courseUser' => $courseUser,
                    'schoolYear' => $schoolYear,
                    'course' => $course,
                    'criteriaData' => $criteriaData,
                    'criteriaByApt' => $criteriaByApt,
                    'aptitudes' => $aptitudes,
                    'acquired' => $acquired[$studnt->id],
                    'proficiencies' => $proficiencies,
                    'acquiredProf' => $acquiredProf[$studnt->id],
                ];
                $pdf = PDF::loadView('pdf.success.view', $data);
                $pdf->stream('document.pdf');
            }
        } else {
            $data = [
                'student' => $student,
                'section' => $section,
                'courseUser' => $courseUser,
                'schoolYear' => $schoolYear,
                'course' => $course,
                'criteriaData' => $criteriaData,
                'criteriaByApt' => $criteriaByApt,
                'aptitudes' => $aptitudes,
                'acquired' => $acquired[$student->id],
                'proficiencies' => $proficiencies,
                'acquiredProf' => $acquiredProf[$student->id],
            ];

            $pdf = PDF::loadView('pdf.success.view', $data);
            return $pdf->stream('document.pdf');
        }





        // $pdf = PDF::loadView('pdf.success.view', $data);
        // return $pdf->download('document.pdf');
        // return view('pdf.success.view', $data);
        // return $pdf->stream('document.pdf');
    }

    public function generatePostponementPdf(Course $course, Section $section, Student $student = null)
    {

        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $course->id)->where('section_students.section_id', $section->id)->select('students.*')->orderBy('students.id')
            ->get();

            $courseStudentData = CourseStudent::where('course_students.course_id', $course->id)->where('course_students.student_id', $student->id)->first();

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

        if ($student === null) {
            foreach ($students as $studnt) {
                $data = [
                    'student' => $studnt,
                    'section' => $section,
                    'courseUser' => $courseUser,
                    'schoolYear' => $schoolYear,
                    'course' => $course,
                    'criteriaData' => $criteriaData,
                    'criteriaByApt' => $criteriaByApt,
                    'aptitudes' => $aptitudes,
                    'acquired' => $acquired[$studnt->id],
                    'proficiencies' => $proficiencies,
                    'acquiredProf' => $acquiredProf[$studnt->id],
                ];
                $pdf = PDF::loadView('pdf.success.view', $data);
                $pdfPostponement = PDF::loadView('pdf.postponement.view', $data);
                //dd($pdfPostponement);
                $pdf->download('document.pdf');
                $pdfPostponement->download('documentPostponement.pdf');
            }
        } else {
            $data = [
                'student' => $student,
                'section' => $section,
                'courseUser' => $courseUser,
                'schoolYear' => $schoolYear,
                'course' => $course,
                'criteriaData' => $criteriaData,
                'criteriaByApt' => $criteriaByApt,
                'aptitudes' => $aptitudes,
                'acquired' => $acquired[$student->id],
                'proficiencies' => $proficiencies,
                'acquiredProf' => $acquiredProf[$student->id],
                'courseStudentData' => $courseStudentData,
            ];
            

            $pdf = PDF::loadView('pdf.success.view', $data);
            $pdfPostponement = PDF::loadView('pdf.postponement.view', $data);
            // $pdfDownload = $pdf->download('document.pdf');
            //  $postponementPdfDownload = $pdfPostponement->download('documentPostponement.pdf');
            // $allPdfPostponement = [$pdfDownload, $postponementPdfDownload];
            // return $allPdfPostponement;

            
            return $pdfPostponement->stream('documentPostponement.pdf');
        }

    }

    













    public function generateFailurePdf(Course $course, Section $section, Student $student = null)
    {

        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $course->id)->where('section_students.section_id', $section->id)->select('students.*')->orderBy('students.id')
            ->get();

            $courseStudentData = CourseStudent::where('course_students.course_id', $course->id)->where('course_students.student_id', $student->id)->first();

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

        if ($student === null) {
            foreach ($students as $studnt) {
                $data = [
                    'student' => $studnt,
                    'section' => $section,
                    'courseUser' => $courseUser,
                    'schoolYear' => $schoolYear,
                    'course' => $course,
                    'criteriaData' => $criteriaData,
                    'criteriaByApt' => $criteriaByApt,
                    'aptitudes' => $aptitudes,
                    'acquired' => $acquired[$studnt->id],
                    'proficiencies' => $proficiencies,
                    'acquiredProf' => $acquiredProf[$studnt->id],
                ];
                $pdf = PDF::loadView('pdf.success.view', $data);
                $pdfPostponement = PDF::loadView('pdf.postponement.view', $data);
                //dd($pdfPostponement);
                $pdf->download('document.pdf');
                $pdfPostponement->download('documentPostponement.pdf');
            }
        } else {
            $data = [
                'student' => $student,
                'section' => $section,
                'courseUser' => $courseUser,
                'schoolYear' => $schoolYear,
                'course' => $course,
                'criteriaData' => $criteriaData,
                'criteriaByApt' => $criteriaByApt,
                'aptitudes' => $aptitudes,
                'acquired' => $acquired[$student->id],
                'proficiencies' => $proficiencies,
                'acquiredProf' => $acquiredProf[$student->id],
                'courseStudentData' => $courseStudentData,
            ];
            

            $pdf = PDF::loadView('pdf.success.view', $data);
            $pdfFailure = PDF::loadView('pdf.failure.view', $data);
            // $pdfDownload = $pdf->download('document.pdf');
            //  $postponementPdfDownload = $pdfPostponement->download('documentPostponement.pdf');
            // $allPdfPostponement = [$pdfDownload, $postponementPdfDownload];
            // return $allPdfPostponement;

            
            return $pdfFailure->stream('documentFailure.pdf');
        }

    }

}
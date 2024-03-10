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
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PdfController extends Controller
{

    public function generatePdf(Course $course, Section $section, Student $student = null)
    {

        $students = CourseStudent::join('students', 'students.id', "=", "course_students.student_id")
            ->join('section_students', 'section_students.student_id', '=', 'students.id')
            ->where('course_students.course_id', $course->id)->where('section_students.section_id', $section->id)->select('students.*')->orderBy('students.id')
            ->get();

        if ($student === null) {
            $courseStudentData = CourseStudent::where('course_students.course_id', $course->id)->get();
        } else {
            $courseStudentData = CourseStudent::where('course_students.course_id', $course->id)->where('course_students.student_id', $student->id)->first();
        }

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

        $passedPdfs = [];
        $adjournedPdfs = [];
        $deniedPdfs = [];

        $failState = null;

        $courseSectionName = str_replace(' ', '-', "{$course->name}-{$section->name}");

        function successOrAdjournmentOrDenied($date_adjourned, $date_eval, $date_denied/*, $courseStudentData*/)
        {

            $date_adjourned = $date_adjourned ? strtotime($date_adjourned) : 0;
            $date_eval = $date_eval ? strtotime($date_eval) : 0;
            $date_denied = $date_denied ? strtotime($date_denied) : 0;

            if ($date_eval > $date_denied && $date_eval > $date_adjourned) {
                return 'success';
            } elseif ($date_adjourned > $date_eval && $date_adjourned > $date_denied) {

                return 'adjourned';
            } elseif ($date_denied > $date_eval && $date_denied > $date_adjourned) {

                return 'denied';
            }
        }
        function getCurrentAcademicYear()
        {
            $currentYear = date('Y');
            $startYear = $currentYear;
            $endYear = $currentYear + 1;

            if (date('n') < 9) {
                $startYear--;
                $endYear--;
            }

            return [
                'start_year' => $startYear,
                'end_year' => $endYear
            ];
        }

        $caca = [];
        if ($student === null) {
            foreach ($students as $studnt) {
                // bon l'ancien système d'indexation des étudiants ne fonctionne plus du jour au lendemain sans AUCUNE raison, donc tant pis, voilà une query pour chaque étudiant individuellement, Optimus Prime serait fier de cette incroyable optimisation
                $studentData = $courseStudentData->where('student_id', $studnt->id)->first();
                
                if ($studentData && $studentData->date_eval !== null) {
                    $fullName = str_replace(' ', '-', "{$studnt->last_name}-{$studnt->first_name}");
                    if (isset($acquiredProf[$studnt->id])) {
                        $profAcquired = $acquiredProf[$studnt->id];
                    } else {
                        $profAcquired = null;
                    }
        
                    $failState = successOrAdjournmentOrDenied($studentData->date_adjourned, $studentData->date_eval, $studentData->date_denied);
        
                    // $caca[] = [
                    //     'student_id' => $studnt->id,
                    //     'fail_state' => $failState
                    // ];
        
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
                        'acquiredProf' => $profAcquired,
                        'courseStudentData' => $studentData, 
                    ];
        
                    $currentAcademicYear = getCurrentAcademicYear();
                    $startYear = $currentAcademicYear['start_year'];
                    $endYear = $currentAcademicYear['end_year'];
        
                    $date_evalComparison = strtotime($studentData->date_eval);
                    $year = date('Y', $date_evalComparison);
                    if ($year >= $startYear && $year <= $endYear) {
                        
                    
                
            
        
        
                        
                    if ($failState === 'success') {
                        $passedPdf = PDF::loadView('pdf.success.view', $data);
                        $passedPdfPath = storage_path('app/public/pdf/' . $fullName . '_' . $courseSectionName . "_fiche-resultats.pdf");
                        $passedPdf->save($passedPdfPath);
                        $passedPdfs[] = $passedPdfPath;
                    } elseif ($failState === 'adjourned') {
                        
                        $adjournedPdf = PDF::loadView('pdf.postponement.view', $data);
                        $adjournedPdfPath = storage_path('app/public/pdf/' . $fullName . '_' . $courseSectionName . "_fiche-ajournement.pdf");
                        $adjournedPdf->save($adjournedPdfPath);
                        $adjournedPdfs[] = $adjournedPdfPath;

                       
                        $successPdf = PDF::loadView('pdf.success.view', $data);
                        $successPdfPath = storage_path('app/public/pdf/' . $fullName . '_' . $courseSectionName . "_fiche-resultats.pdf");
                        $successPdf->save($successPdfPath);
                        $passedPdfs[] = $successPdfPath;
                    } elseif ($failState === 'denied') {
                        
                        $deniedPdf = PDF::loadView('pdf.failure.view', $data);
                        $deniedPdfPath = storage_path('app/public/pdf/' . $fullName . '_' . $courseSectionName . "_fiche-refus.pdf");
                        $deniedPdf->save($deniedPdfPath);
                        $deniedPdfs[] = $deniedPdfPath;

                        
                        $successPdf = PDF::loadView('pdf.success.view', $data);
                        $successPdfPath = storage_path('app/public/pdf/' . $fullName . '_' . $courseSectionName . "_fiche-resultats.pdf");
                        $successPdf->save($successPdfPath);
                        $passedPdfs[] = $successPdfPath;
                    }
                    
                   
                }
            }}
         // return $caca;
            $pdfs = array_merge($passedPdfs, $adjournedPdfs, $deniedPdfs);

            $zipFileName =  $courseSectionName  . '.zip';
            $zipDirectory = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pdf');
            $zipPath = $zipDirectory . DIRECTORY_SEPARATOR . $zipFileName;

            if (!file_exists($zipDirectory)) {
                mkdir($zipDirectory, 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($pdfs as $pdfFile) {
                  
                    $relativePdfPath =  basename($pdfFile);
                    $zip->addFile($pdfFile, $relativePdfPath);
                }

                $zip->close();

                if (file_exists($zipPath)) {
                    
                    $response = response()->download($zipPath)->deleteFileAfterSend(true);
                    return $response;
                } else {
                    echo "Erreur: Impossible de créer le fichier ZIP - $zipPath <br>";
                }
            } else {
                echo "Erreur: Impossible de créer le fichier ZIP - $zipPath <br>";
            } 
    
        }else {
           // dd($acquiredProf);
            if ($courseStudentData->date_eval !== null) {
            $failState = successOrAdjournmentOrDenied($courseStudentData->date_adjourned, $courseStudentData->date_eval, $courseStudentData->date_denied/*,$courseStudentData*/);
    if(isset($acquiredProf[$student->id])){
        $profAcquired = $acquiredProf[$student->id];}else{
            $profAcquired = null;
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
        'acquired' => $acquired[$student->id],
        'proficiencies' => $proficiencies,
       
        'acquiredProf' => $profAcquired,
        'courseStudentData' => $courseStudentData,
    ];
    $fullName = str_replace(' ', '-', "{$student->last_name}-{$student->first_name}");

    if ($failState === 'success') {
   
        $successPdf = PDF::loadView('pdf.success.view', $data);
        $successPdfPath = storage_path('app/public/pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf');
         return $successPdf->download($fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf');
        
        //$pdfs[] = 'pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf';
    } elseif ($failState === 'adjourned') {
   
        $adjournedPdf = PDF::loadView('pdf.postponement.view', $data);
        $adjournedPdfPath = storage_path('app/public/pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-ajournement.pdf');
        $adjournedPdf->save($adjournedPdfPath);
       
        $pdfs[] = 'pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-ajournement.pdf';

        $successPdf = PDF::loadView('pdf.success.view', $data);
        $successPdfPath = storage_path('app/public/pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf');
        $successPdf->save($successPdfPath);
     
        $pdfs[] = 'pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf';
    } elseif ($failState === 'denied') {
        
        $deniedPdf = PDF::loadView('pdf.failure.view', $data);
        $deniedPdfPath = storage_path('app/public/pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-refus.pdf');
        $deniedPdf->save($deniedPdfPath);
      
        $pdfs[] = 'pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-refus.pdf';

        $successPdf = PDF::loadView('pdf.success.view', $data);
        $successPdfPath = storage_path('app/public/pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf');
        $successPdf->save($successPdfPath);
        
        $pdfs[] = 'pdf/' . $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '_fiche-resultats.pdf';
    }
}

    $zipFileName =  $fullName . "_" . str_replace(' ', '-', "{$course->name}-{$section->name}") . '.zip';
    $zipDirectory = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pdf');
    $zipPath = $zipDirectory . DIRECTORY_SEPARATOR . $zipFileName;

    if (!file_exists($zipDirectory)) {
        mkdir($zipDirectory, 0755, true);
    }

    $zip = new ZipArchive;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($pdfs as $pdfFile) {
            $pdfFilePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $pdfFile);

            if (file_exists($pdfFilePath)) {
                $zip->addFile($pdfFilePath, basename($pdfFile));
            } else {
                echo "Erreur: Fichier non trouvé - $pdfFilePath <br>";
            }
        }

        $zip->close();
        if (file_exists($zipPath)) {
            $response = response()->download($zipPath)->deleteFileAfterSend(true);

            foreach ($pdfs as $pdfFile) {
                $pdfFilePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $pdfFile);

                if (file_exists($pdfFilePath)) {
                    unlink($pdfFilePath);
                } else {
                    echo "Erreur: Fichier non trouvé - $pdfFilePath <br>";
                }
            }
            return $response;
        } else {
            echo "Erreur: Impossible de créer fichier zip - $zipPath <br>";
        }
    } else {
        echo "Erreur: Impossible de créer fichier zip - $zipPath <br>";
    }
}
    }}
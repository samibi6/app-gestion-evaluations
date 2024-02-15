<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentStoreRequest;
use App\Models\Section;
use App\Models\SectionStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {

        $students = Student::get();
        $sections = Section::get();

        $sectionsByStudents =
            Student::join('section_students', 'students.id', '=', 'section_students.student_id')
            ->join('sections', 'section_students.section_id', '=', 'sections.id')
            ->select('students.id as student-id', 'sections.*')
            ->get()
            ->groupBy('student-id');


        return Inertia::render('Students/Index', [
            'students' => $students,
            'sections' => $sections,
            'sectionsByStudents' => $sectionsByStudents,
        ]);
    }

    public function store(StudentStoreRequest $request)
    {
        dd($request->all());
        $student = Student::make();
        $student->first_name = $request->validated()['first_name'];
        $student->last_name = $request->validated()['last_name'];
        $student->email = $request->validated()['email'];

        $student->save();


        $sectionStudent = SectionStudent::make();
        $sectionStudent->student_id = $student->id;
        $sectionStudent->section_id = $request->validated()['section'];

        $sectionStudent->save();


        $request->session()->flash('flash.banner', 'L étudiant a bien été créé.');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Models\Section;
use App\Models\SectionStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // $students = Student::where('first_name', 'LIKE', '%' . $request->query('search') . '%')
        //     ->paginate(8)
        //     ->withQueryString();

        $searchQuery = strtolower($request->query('search'));

        $students = Student::whereRaw('LOWER(first_name) LIKE ?', ["%{$searchQuery}%"])
            ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchQuery}%"])
            ->paginate(8)
            ->withQueryString();

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
        $student = Student::make();
        $student->first_name = $request->validated()['first_name'];
        $student->last_name = $request->validated()['last_name'];
        $student->save();


        $sectionStudent = SectionStudent::make();
        $sectionStudent->student_id = $student->id;
        $sectionStudent->section_id = $request->validated()['section'];

        $sectionStudent->save();


        $request->session()->flash('flash.banner', 'L étudiant a bien été créé.');
        return redirect()->back();
    }
    public function edit(Student $student)
    {

        $students = Student::get();
        $sections = Section::get();

        /*$sectionsByCurrentStudent = Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
            ->where('course_sections.course_id', $course->id)
            ->select('sections.*')
            ->get();*/

        $sectionsByCurrentStudent =
            Student::join('section_students', 'students.id', '=', 'section_students.student_id')
            ->join('sections', 'section_students.section_id', '=', 'sections.id')
            ->where('section_students.student_id', $student->id)
            ->select(/*'students.id as student-id',*/'sections.*')
            ->get();



        return Inertia::render('Students/Edit', [
            'student' => $student,
            'sections' => $sections,
            'sectionsByCurrentStudent' => $sectionsByCurrentStudent,
        ]);
    }

    public function update(StudentUpdateRequest $request, Student $student)
    {

        $student->update([
            'first_name' => $request->validated()['first_name'],
            'last_name' => $request->validated()['last_name'],
        ]);

        $sectionStudent = SectionStudent::where('student_id', $student->id)->first();
        if ($sectionStudent) {
            $sectionStudent->update([
                'section_id' => $request->validated()['section'],

            ]);
        } else {

            $sectionStudent = SectionStudent::make([
                'student_id' => $student->id,
                'section_id' => $request->validated()['section'],

            ]);
            $sectionStudent->save();
        }


        $request->session()->flash('flash.banner', 'Les données de l\'étudiant ont bien été modifiées.');
        return redirect()->route('students.index');
    }

    public function delete(Student $student)
    {

        $student->delete();

        session()->flash('flash.banner', 'L\'étudiant à bien été supprimé');

        return redirect()->route('students.index');
    }
}

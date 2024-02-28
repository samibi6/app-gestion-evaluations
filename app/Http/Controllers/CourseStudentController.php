<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseSectionStoreRequest;
use App\Http\Requests\CourseStudentStoreRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseStudent;
use App\Models\Section;
use App\Models\SectionStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseStudentController extends Controller
{
    public function index(Request $request)
    {

        $sections = Section::get();
        $courses = Course::get();
        $students = Student::get();

        $sectionsByCourses = Course::join('course_sections', 'courses.id', '=', 'course_sections.course_id')
            ->join('sections', 'course_sections.section_id', '=', 'sections.id')
            ->select('courses.id as course-id', 'sections.*') //le groupBy ne fonctionne pas correctement sans l'alias, même en précisant courses.id
            ->get()
            ->groupBy('course-id');

        $studentsBySections =  // ça servira à afficher en premier les élèves des sections du cours choisi
            Section::join('section_students', 'sections.id', '=', 'section_students.section_id')
            ->join('students', 'section_students.student_id', '=', 'students.id')
            ->select('sections.id as section-id', 'students.*')
            ->get()
            ->groupBy('section-id');

        $studentsByCourses =  // ça servira à afficher uniquement les élèves non-inscrits au cours choisi
            Course::join('course_students', 'courses.id', '=', 'course_students.course_id')
            ->join('students', 'course_students.student_id', '=', 'students.id')
            ->select('courses.id as course-id', 'students.*')
            ->get()
            ->groupBy('course-id');

        $courseSectionDB = CourseSection::get();

        $message = $request->session()->get('success');

        return Inertia::render('CourseStudents/Index', [
            'sections' => $sections,
            'courses'  => $courses,
            'students' => $students,
            'sectionsByCourses' => $sectionsByCourses,
            'studentsBySections' => $studentsBySections,
            'studentsByCourses' => $studentsByCourses,
            'courseSectionDB' => $courseSectionDB,
            'message' => $message
        ]);
    }

    public function store(CourseStudentStoreRequest $request)
    {
        foreach ($request->validated()['student'] as $id => $value) {

            $courseStudent = CourseStudent::make();
            $courseStudent->student_id = $id;
            $courseStudent->course_id = $request->validated()['course'];
            $courseStudent->save();

            $sectionStudent = SectionStudent::where('student_id', $id)
                ->where('section_id', $request->validated()['section'])
                ->exists();

            if (!$sectionStudent) {
                $sectionStudent = SectionStudent::make([
                    'student_id' => $id,
                    'section_id' => $request->validated()['section'],
                ]);
                $sectionStudent->save();
            }
        }
    }


    public function delete(Request $request)
    {
        $validatedData = $request->validate([
            'idStudent' => 'required|integer',
            'idCourse' => 'required|integer',
        ]);
        $entry = CourseStudent::where('student_id', $request['idStudent'])->where('course_id', $request['idCourse'])->firstOrFail();
        $entry->delete();
        return redirect()->route('courseStudents.index')->with('success', 'Entry deleted successfully');
    }
}

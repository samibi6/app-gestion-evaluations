<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseSectionStoreRequest;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseSectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::get();
        $courses = Course::get();

        $coursesBySection = Section::join('course_sections', 'sections.id', '=', 'course_sections.section_id')
            ->join('courses', 'courses.id', '=', 'course_sections.course_id')
            ->select('sections.id as section-id', 'courses.*')
            ->get()
            ->groupBy('section-id');

        $courseSectionDB = CourseSection::get();

        $message = $request->session()->get('success');

        return Inertia::render('CoursSections/Index', [
            'sections' => $sections,
                'courses'  => $courses,
            'coursesBySection' => $coursesBySection,
            'courseSectionDB' => $courseSectionDB,
            'message' => $message
        ]);
    }

    public function store(CourseSectionStoreRequest $request)
    {
        foreach ($request->validated()['course'] as $id => $value) {
            $courseSection = CourseSection::make();
            $courseSection->course_id = $id;
            $courseSection->section_id = $request->validated()['section'];
            $courseSection->save();
        }
    }

    public function delete(Request $request)
    {
        $validatedData = $request->validate([
            'idC' => 'required|integer', // Validation rules for idC
            'idS' => 'required|integer', // Validation rules for idS
        ]);
        $entry = CourseSection::where('course_id', $request['idC'])->where('section_id', $request['idS'])->firstOrFail();
        $entry->delete();
        return redirect()->route('coursSections.index')->with('success', 'Entry deleted successfully');
    }
}

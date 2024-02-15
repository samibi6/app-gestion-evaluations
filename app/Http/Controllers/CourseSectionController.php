<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseSectionStoreRequest;
use App\Models\Course;
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
            ->select('sections.id as section-id', 'courses.*', 'course_sections.year')
            ->get()
            ->groupBy('section-id');

        return Inertia::render('Sections/Index', [
            'sections' => $sections,
            'courses'  => $courses,
            'coursesBySection' => $coursesBySection,
        ]);
    }

    public function store(Request $request)
    {
        dd($request->all());
    }
}

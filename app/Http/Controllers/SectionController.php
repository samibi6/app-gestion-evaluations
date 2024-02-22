<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Models\CourseSection;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::get();

        $message = $request->session()->get('success');

        return Inertia::render('Sections/Index', [
            'sections' => $sections,
            'message' => $message
        ]);
    }

    public function store(SectionStoreRequest $request)
    {
        $section = Section::make();
        $section->name = $request->validated()['name'];
        $section->save();
    }

    public function edit(Section $section)
    {
        return Inertia::render("Sections/Edit", ["section" => $section]);
    }

    public function update(SectionUpdateRequest $request, Section $section)
    {
        $section->update([
            'name' => $request->validated()['name'],
        ]);
        return redirect()->route('sections.index')->with('success', 'Section modified successfully');
    }

    public function delete(Section $section)
    {
        $section->delete();
        $courseSection = CourseSection::where('section_id', $section->id)->get();
        foreach ($courseSection as $line) {
            $line->delete();
        }
        return redirect()->route('sections.index')->with('success', 'Section deleted successfully');
    }
}

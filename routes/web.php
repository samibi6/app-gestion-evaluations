<?php

use App\Http\Controllers\AptitudeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\EvalController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SectionController;
use App\Models\Aptitude;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/students', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'delete'])->name('students.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/invites', [InviteController::class, 'store'])->name('invites.store');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/sections/edit/{section}', [SectionController::class, 'edit'])->name('sections.edit');
    Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    // Route::put('/sections/{Section}', [SectionController::class, 'status'])->name('sections.status');
    Route::delete('/sections/{section}', [SectionController::class, 'delete'])->name('sections.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/coursSections', [CourseSectionController::class, 'index'])->name('coursSections.index');
    Route::post('/coursSections', [CourseSectionController::class, 'store'])->name('coursSections.store');
    // Route::put('/coursSections/{Section}', [CourseSectionController::class, 'status'])->name('coursSections.status');
    Route::delete('/coursSections', [CourseSectionController::class, 'delete'])->name('coursSections.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/cours', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/cours', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/cours/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::patch('/cours/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/cours/{course}', [CourseController::class, 'delete'])->name('courses.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/coursStudents', [CourseStudentController::class, 'index'])->name('courseStudents.index');
    Route::post('/coursStudents', [CourseStudentController::class, 'store'])->name('courseStudents.store');
    // Route::put('/coursSections/{Section}', [CourseSectionController::class, 'status'])->name('coursSections.status');
    Route::delete('/coursStudents', [CourseStudentController::class, 'delete'])->name('courseStudents.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/acquis', [AptitudeController::class, 'index'])->name('aptitudes.index');
    Route::post('/acquis/storeAptitude', [AptitudeController::class, 'storeAptitude'])->name('aptitudes.storeAptitude');
    Route::post('/acquis/storeCriteria', [AptitudeController::class, 'storeCriteria'])->name('aptitudes.storeCriteria');
    Route::put('/acquis/{aptitude}/updateAptitude', [AptitudeController::class, 'updateAptitude'])->name('aptitudes.updateAptitude');
    Route::put('/acquis/{criteria}/updateCriteria', [AptitudeController::class, 'updateCriteria'])->name('aptitudes.updateCriteria');
    Route::delete('/acquis/{aptitude}/deleteAptitude', [AptitudeController::class, 'deleteAptitude'])->name('aptitudes.deleteAptitude');
    Route::delete('/acquis/{criteria}/deleteCriteria', [AptitudeController::class, 'deleteCriteria'])->name('aptitudes.deleteCriteria');
    Route::post('/acquis/storeProficiency', [AptitudeController::class, 'storeProficiency'])->name('aptitudes.storeProficiency');
    Route::patch('/acquis/{proficiency}/updateProficiency', [AptitudeController::class, 'updateProficiency'])->name('aptitudes.updateProficiency');
    Route::delete('/acquis/{proficiency}/deleteProficiency', [AptitudeController::class, 'deleteProficiency'])->name('aptitudes.deleteProficiency');
    Route::patch('/acquis/{course}/updateLead', [AptitudeController::class, 'updateLead'])->name('aptitudes.updateLead');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/evals', [EvalController::class, 'index'])->name('evals.index');
    Route::get('/evals/show/{courseId}/{sectionId}', [EvalController::class, 'show'])->name('evals.show');
    Route::get('/evals/edit/{courseId}/{studentId}/{sectionId}', [EvalController::class, 'edit'])->name('evals.edit');
    Route::post('/evals/{studentId}/{courseId}/{sectionId}', [EvalController::class, 'store'])->name('evals.store');
    // Route::put('/evals/{course}', [EvalController::class, 'status'])->name('coursSections.status');
    Route::delete('/evals', [EvalController::class, 'delete'])->name('evals.delete');

    Route::get('/evals/echec/{courseId}/{sectionId}/{studentId}', [EvalController::class, 'fail'])->name('evals.fail');
    Route::get('/evals/ajournement/{courseId}/{sectionId}/{studentId}', [EvalController::class, 'adjournment'])->name('evals.adjournment');
    Route::post('/evals/ajournement/{courseId}/{sectionId}/{studentId}', [EvalController::class, 'storeAdjournment'])->name('evals.storeAdjournment');
    Route::get('/evals/refus/{courseId}/{sectionId}/{studentId}', [EvalController::class, 'denied'])->name('evals.denied');
    Route::post('/evals/refus/{courseId}/{sectionId}/{studentId}', [EvalController::class, 'storeDenied'])->name('evals.storeDenied');
  
});

Route::middleware(['auth'])->group(function () {
    Route::get('/pdf/success/{course}/{section}/{student?}', [PdfController::class, 'generateSuccessPdf'])->name('pdf.success');
    Route::get('/pdf/failure/{course}/{section}/{student?}', [PdfController::class, 'generateFailurePdf']);
    Route::get('/pdf/postponement/{course}/{section}/{student?}', [PdfController::class, 'generatePostponementPdf']);
});

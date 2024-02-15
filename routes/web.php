<?php


use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\SectionController;
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
    // Route::put('/students/{Student}', [StudentController::class, 'status'])->name('Students.status');
    // Route::delete('/students/{Student}', [StudentController::class, 'delete'])->name('Students.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/sections', [CourseSectionController::class, 'index'])->name('sections.index');
    Route::post('/sections', [CourseSectionController::class, 'store'])->name('sections.store');
    // Route::put('/sections/{Section}', [SectionController::class, 'status'])->name('sections.status');
    // Route::delete('/sections/{Section}', [SectionController::class, 'delete'])->name('sections.delete');
});

Route::middleware('auth', HandlePrecognitiveRequests::class)->group(function () {
    Route::get('/cours', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/cours', [CourseController::class, 'store'])->name('courses.store');
        /*Route::put('/Students/{Student}', [StudentController::class, 'status'])->name('Students.status');
    Route::delete('/Students/{Student}', [StudentController::class, 'delete'])->name('Students.delete')*/;
});

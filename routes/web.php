<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicAttendanceController;

Route::get('/', function () {
    return view('welcome');
});

// Public Quick Attendance
Route::get('/quick-attendance', [PublicAttendanceController::class, 'index'])->name('quick-attendance.index');
Route::post('/quick-attendance', [PublicAttendanceController::class, 'store'])->name('quick-attendance.store');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;

// Super Admin Routes
Route::prefix('super_admin')->name('super_admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'super_admin') return redirect()->route(auth()->user()->role . '.dashboard');
        return view('super_admin.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/attendance/import', [AttendanceController::class, 'import'])->name('attendance.import');
    Route::get('/attendance/template', [AttendanceController::class, 'downloadTemplate'])->name('attendance.template');
    Route::post('/attendance/{attendance}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve');

    // Evaluation
    Route::get('/evaluation/start', [EvaluationController::class, 'start'])->name('evaluation.start');
    Route::post('/evaluation/submit', [EvaluationController::class, 'submit'])->name('evaluation.submit');
    Route::delete('/evaluation/destroy-all', [EvaluationController::class, 'destroyAll'])->name('evaluation.destroyAll');
    Route::post('/evaluation/import', [EvaluationController::class, 'import'])->name('evaluation.import');
    Route::get('/evaluation/template', [EvaluationController::class, 'downloadTemplate'])->name('evaluation.template');
    Route::resource('evaluation', EvaluationController::class);
    Route::get('/evaluation-results', [EvaluationController::class, 'results'])->name('evaluation.results');
    Route::get('/evaluation-results/export', [EvaluationController::class, 'exportResults'])->name('evaluation.results.export');
    Route::delete('/evaluation-results/{result}', [EvaluationController::class, 'destroyResult'])->name('evaluation.results.destroy');
    Route::get('/evaluation-results/{result}/grade', [EvaluationController::class, 'grade'])->name('evaluation.grade');
    Route::post('/evaluation-results/{result}/grade', [EvaluationController::class, 'storeGrade'])->name('evaluation.storeGrade');
    Route::post('/evaluation/update-passing-grade', [EvaluationController::class, 'updatePassingGrade'])->name('evaluation.update_passing_grade');

    // Materials
    Route::resource('materials', MaterialController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'admin') return redirect()->route(auth()->user()->role . '.dashboard');
        return view('admin.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Users (Admin can manage users too based on controller logic)
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create-manual', [AttendanceController::class, 'createManual'])->name('attendance.createManual');
    Route::post('/attendance/store-manual', [AttendanceController::class, 'storeManual'])->name('attendance.storeManual');
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/attendance/import', [AttendanceController::class, 'import'])->name('attendance.import');
    Route::get('/attendance/template', [AttendanceController::class, 'downloadTemplate'])->name('attendance.template');
    Route::post('/attendance/{attendance}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve');
    
    // Evaluation (View Results & Manage)
    Route::get('/evaluation-results', [EvaluationController::class, 'results'])->name('evaluation.results');
    Route::get('/evaluation-results/export', [EvaluationController::class, 'exportResults'])->name('evaluation.results.export');
    Route::resource('evaluation', EvaluationController::class);

    // Materials
    Route::resource('materials', MaterialController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Operator Routes
Route::prefix('operator')->name('operator.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'operator') return redirect()->route(auth()->user()->role . '.dashboard');
        return view('operator.dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    
    // Evaluation
    Route::get('/evaluation/start', [EvaluationController::class, 'start'])->name('evaluation.start');
    Route::post('/evaluation/submit', [EvaluationController::class, 'submit'])->name('evaluation.submit');
    Route::get('/evaluation-results', [EvaluationController::class, 'results'])->name('evaluation.results');
    
    // Materials (View Only)
    Route::resource('materials', MaterialController::class)->only(['index', 'show']);
});

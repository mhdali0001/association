<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DuplicateMembersController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MaritalStatusController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberImportController;
use App\Http\Controllers\VerificationStatusController;
use Illuminate\Support\Facades\Route;

// Welcome
Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('admin')->group(function () {
        Route::get('/users',           [UserController::class,  'index'])        ->name('users.index');
        Route::get('/users/create',    [AuthController::class,  'showRegister']) ->name('users.create');
        Route::post('/users',          [AuthController::class,  'register'])     ->name('users.store');
        Route::delete('/users/{user}', [UserController::class,  'destroy'])      ->name('users.destroy');
        Route::get('/activity-logs',   [ActivityLogController::class, 'index'])  ->name('activity-logs.index');
        Route::resource('expenses',    ExpenseController::class);
    });

    Route::get('/members/import',          [MemberImportController::class, 'show'])    ->name('members.import.show');
    Route::post('/members/import',         [MemberImportController::class, 'store'])   ->name('members.import.store');
    Route::get('/members/import/template', [MemberImportController::class, 'template'])->name('members.import.template');
    Route::get('/members/import/{importResult}/status',        [MemberImportController::class, 'status'])->name('members.import.status');
    Route::post('/members/import/{importResult}/chunk',        [MemberImportController::class, 'chunk']) ->name('members.import.chunk');
    Route::resource('members', MemberController::class);
    Route::get('/members-duplicates', [DuplicateMembersController::class, 'index'])->name('members.duplicates');
    Route::resource('marital-statuses', MaritalStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('associations', AssociationController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('verification-statuses', VerificationStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    // Donations — monthly must come before resource to avoid conflict with {donation}
    Route::get('/donations/monthly', [DonationController::class, 'monthly'])->name('donations.monthly');
    Route::resource('donations', DonationController::class)->except(['show']);
});

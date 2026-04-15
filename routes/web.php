<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DelegateController;
use App\Http\Controllers\PaymentReviewController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DuplicateMembersController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MaritalStatusController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberImageController;
use App\Http\Controllers\PendingChangeController;
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
        // Budget & Beneficiaries
        Route::get('/budget',                          [BudgetController::class,     'index'])  ->name('budget.index');
        Route::post('/budget/set-total',               [BudgetController::class,     'setTotal'])->name('budget.set-total');
        Route::post('/beneficiaries',                  [BeneficiaryController::class,'store'])  ->name('beneficiaries.store');
        Route::put('/beneficiaries/{beneficiary}',     [BeneficiaryController::class,'update']) ->name('beneficiaries.update');
        Route::delete('/beneficiaries/{beneficiary}',  [BeneficiaryController::class,'destroy'])->name('beneficiaries.destroy');
        Route::get('/payment-review',                    [PaymentReviewController::class, 'index'])        ->name('payment-review.index');
        Route::get('/payment-review/export-matched',     [PaymentReviewController::class, 'exportMatched']) ->name('payment-review.export-matched');
        Route::get('/payment-review/import',             [PaymentReviewController::class, 'importShow'])   ->name('payment-review.import.show');
        Route::post('/payment-review/import',            [PaymentReviewController::class, 'importStore'])  ->name('payment-review.import.store');
        Route::get('/payment-review/duplicate-ibans',    [PaymentReviewController::class, 'duplicateIbans'])->name('payment-review.duplicate-ibans');
        Route::post('/payment-review/{member}',          [PaymentReviewController::class, 'store'])        ->name('payment-review.store');
        Route::patch('/payment-review/{member}/iban',    [PaymentReviewController::class, 'updateIban'])   ->name('payment-review.update-iban');
        Route::get('/pending-changes',                          [PendingChangeController::class, 'index'])  ->name('pending-changes.index');
        Route::get('/pending-changes/{pendingChange}',          [PendingChangeController::class, 'show'])   ->name('pending-changes.show');
        Route::post('/pending-changes/{pendingChange}/approve',      [PendingChangeController::class, 'approve'])          ->name('pending-changes.approve');
        Route::post('/pending-changes/{pendingChange}/approve-edit', [PendingChangeController::class, 'approveWithEdit']) ->name('pending-changes.approve-edit');
        Route::post('/pending-changes/{pendingChange}/reject',       [PendingChangeController::class, 'reject'])          ->name('pending-changes.reject');
        Route::post('/pending-changes/{pendingChange}/reopen',       [PendingChangeController::class, 'reopen'])          ->name('pending-changes.reopen');
        Route::post('/pending-changes/{pendingChange}/revoke',       [PendingChangeController::class, 'revoke'])          ->name('pending-changes.revoke');
        // Config tables — admin only (index visible to all auth users below)
    });

    Route::get('/my-requests', [PendingChangeController::class, 'myRequests'])->name('pending-changes.my');

    // Config tables — all authenticated users (write operations go through pending changes for non-admins)
    Route::resource('marital-statuses',      MaritalStatusController::class)     ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('associations',          AssociationController::class)        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('verification-statuses', VerificationStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('final-statuses',        \App\Http\Controllers\FinalStatusController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/members/export',                   [MemberController::class, 'export'])             ->name('members.export');
    Route::delete('/members/bulk-destroy',          [MemberController::class, 'bulkDestroy'])        ->name('members.bulk-destroy');
    Route::patch('/members/bulk-update',            [MemberController::class, 'bulkUpdate'])          ->name('members.bulk-update');
    Route::patch('/members/{member}/final-status', [MemberController::class, 'updateFinalStatus'])->name('members.final-status.update');
    Route::get('/members/bulk-amount',  [MemberController::class, 'bulkAmountShow']) ->name('members.bulk-amount');
    Route::post('/members/bulk-amount', [MemberController::class, 'bulkAmountApply'])->name('members.bulk-amount.apply');
    Route::get('/members/import',                 [MemberImportController::class, 'show'])    ->name('members.import.show');
    Route::post('/members/import',                [MemberImportController::class, 'store'])   ->name('members.import.store');
    Route::get('/members/import-gender',          [\App\Http\Controllers\GenderImportController::class, 'show'])  ->name('members.import-gender.show');
    Route::post('/members/import-gender',         [\App\Http\Controllers\GenderImportController::class, 'store']) ->name('members.import-gender.store');
    Route::get('/members/import/template', [MemberImportController::class, 'template'])->name('members.import.template');
    Route::get('/members/import/{importResult}/status',        [MemberImportController::class, 'status'])->name('members.import.status');
    Route::post('/members/import/{importResult}/chunk',        [MemberImportController::class, 'chunk']) ->name('members.import.chunk');
    Route::resource('members', MemberController::class);
    Route::patch('/members/{member}/address', [MemberController::class, 'updateAddress'])->name('members.address.update');
    Route::patch('/members/{member}/region',  [MemberController::class, 'updateRegion']) ->name('members.region.update');
    Route::get('/member-images',                     [MemberImageController::class, 'index'])      ->name('member-images.index');
    Route::post('/member-images',                    [MemberImageController::class, 'storeGlobal'])->name('member-images.store-global');
    Route::get('/member-images/{memberImage}/edit',  [MemberImageController::class, 'edit'])       ->name('member-images.edit');
    Route::patch('/member-images/{memberImage}',     [MemberImageController::class, 'update'])     ->name('member-images.update');
    Route::post('/members/{member}/images',          [MemberImageController::class, 'store'])      ->name('member-images.store');
    Route::delete('/member-images/{memberImage}',    [MemberImageController::class, 'destroy'])    ->name('member-images.destroy');
    Route::get('/members-duplicates', [DuplicateMembersController::class, 'index'])->name('members.duplicates');
    Route::get('/age-statistics',     [\App\Http\Controllers\AgeStatisticsController::class, 'index'])->name('age-statistics.index');
    Route::get('/delegates',          [DelegateController::class, 'index'])         ->name('delegates.index');
    Route::get('/delegates/{delegate}',[DelegateController::class, 'show'])         ->name('delegates.show');
    Route::resource('field-visit-statuses', \App\Http\Controllers\FieldVisitStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('house-types', \App\Http\Controllers\HouseTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('regions', \App\Http\Controllers\RegionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/members/{member}/field-visits',                          [\App\Http\Controllers\FieldVisitController::class, 'store'])  ->name('field-visits.store');
    Route::put('/members/{member}/field-visits/{fieldVisit}',             [\App\Http\Controllers\FieldVisitController::class, 'update'])        ->name('field-visits.update');
    Route::patch('/members/{member}/field-visits/{fieldVisit}/adjust',   [\App\Http\Controllers\FieldVisitController::class, 'adjustAmount'])  ->name('field-visits.adjust');
    Route::delete('/members/{member}/field-visits/{fieldVisit}',          [\App\Http\Controllers\FieldVisitController::class, 'destroy'])->name('field-visits.destroy');
    // Donations — named routes before resource to avoid conflict with {donation}
    Route::get('/donations/monthly',       [DonationController::class, 'monthly'])     ->name('donations.monthly');
    Route::post('/donations/monthly/quick',[DonationController::class, 'quickDonate'])->name('donations.monthly.quick');
    Route::resource('donations', DonationController::class)->except(['show']);
});

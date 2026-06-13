<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BulkRevertController;
use App\Http\Controllers\StatisticsController;
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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\CustomExportController;
use Illuminate\Support\Facades\Route;

// Welcome
Route::get('/', function () {
    return view('welcome');
});

// Employee self-service portal (public)
Route::get('/portal',          [EmployeePortalController::class, 'login'])        ->name('employee-portal.login');
Route::post('/portal',         [EmployeePortalController::class, 'authenticate']) ->name('employee-portal.authenticate');
Route::get('/portal/me',       [EmployeePortalController::class, 'dashboard'])    ->name('employee-portal.dashboard');
Route::post('/portal/logout',  [EmployeePortalController::class, 'logout'])       ->name('employee-portal.logout');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (protected)
Route::middleware('auth')->group(function () {
    Route::get('/password/change',  [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword'])    ->name('password.update');

    Route::get('/dashboard',                        [DashboardController::class,  'index'])           ->name('dashboard');
    Route::get('/dashboard/user-activity/{user}',   [DashboardController::class,  'userWeekActivity'])->name('dashboard.user-activity');
    Route::post('/dashboard/revert-activity/{log}', [DashboardController::class,  'revertActivity'])  ->name('dashboard.revert-activity');
    Route::get('/statistics',                       [StatisticsController::class, 'index'])            ->name('statistics');
    Route::middleware('admin')->group(function () {
        Route::get('/users',           [UserController::class,  'index'])        ->name('users.index');
        Route::get('/users/create',    [AuthController::class,  'showRegister']) ->name('users.create');
        Route::post('/users',          [AuthController::class,  'register'])     ->name('users.store');
        Route::delete('/users/{user}', [UserController::class,  'destroy'])      ->name('users.destroy');
        Route::get('/activity-logs',                          [ActivityLogController::class,  'index'])  ->name('activity-logs.index');
        Route::get('/bulk-revert',                            [BulkRevertController::class,   'index'])  ->name('bulk-revert.index');
        Route::post('/bulk-revert/{session}',                 [BulkRevertController::class,   'revert']) ->name('bulk-revert.revert');
        Route::get('/archive',         [\App\Http\Controllers\ArchiveController::class, 'index'])->name('archive.index');
        Route::resource('expenses',    ExpenseController::class);
        // Budget & Beneficiaries
        Route::get('/budget',                          [BudgetController::class,     'index'])  ->name('budget.index');
        Route::post('/budget/set-total',               [BudgetController::class,     'setTotal'])->name('budget.set-total');
        Route::post('/beneficiaries',                  [BeneficiaryController::class,'store'])  ->name('beneficiaries.store');
        Route::put('/beneficiaries/{beneficiary}',     [BeneficiaryController::class,'update']) ->name('beneficiaries.update');
        Route::delete('/beneficiaries/{beneficiary}',  [BeneficiaryController::class,'destroy'])->name('beneficiaries.destroy');
        Route::get('/payment-review',                    [PaymentReviewController::class, 'index'])        ->name('payment-review.index');
        Route::get('/payment-review/export-matched',          [PaymentReviewController::class, 'exportMatched'])         ->name('payment-review.export-matched');
        Route::get('/payment-review/export-matched-reviewed', [PaymentReviewController::class, 'exportMatchedReviewed']) ->name('payment-review.export-matched-reviewed');
        Route::get('/payment-review/import',             [PaymentReviewController::class, 'importShow'])   ->name('payment-review.import.show');
        Route::post('/payment-review/import',            [PaymentReviewController::class, 'importStore'])  ->name('payment-review.import.store');
        Route::get('/payment-review/duplicate-ibans',    [PaymentReviewController::class, 'duplicateIbans'])->name('payment-review.duplicate-ibans');
        Route::get('/payment-review/recent-ibans',       [PaymentReviewController::class, 'recentIbans'])   ->name('payment-review.recent-ibans');
        Route::post('/payment-review/bulk-delete',        [PaymentReviewController::class, 'bulkDelete'])   ->name('payment-review.bulk-delete');
        Route::post('/payment-review/{member}',          [PaymentReviewController::class, 'store'])        ->name('payment-review.store');
        Route::patch('/payment-review/{member}/iban',    [PaymentReviewController::class, 'updateIban'])   ->name('payment-review.update-iban');
        Route::get('/pending-changes',                          [PendingChangeController::class, 'index'])  ->name('pending-changes.index');
        Route::get('/pending-changes/{pendingChange}',          [PendingChangeController::class, 'show'])   ->name('pending-changes.show');
        Route::post('/pending-changes/{pendingChange}/approve',      [PendingChangeController::class, 'approve'])          ->name('pending-changes.approve');
        Route::post('/pending-changes/{pendingChange}/approve-edit', [PendingChangeController::class, 'approveWithEdit']) ->name('pending-changes.approve-edit');
        Route::post('/pending-changes/{pendingChange}/reject',       [PendingChangeController::class, 'reject'])          ->name('pending-changes.reject');
        Route::post('/pending-changes/{pendingChange}/reopen',       [PendingChangeController::class, 'reopen'])          ->name('pending-changes.reopen');
        Route::post('/pending-changes/{pendingChange}/revoke',       [PendingChangeController::class, 'revoke'])          ->name('pending-changes.revoke');
        Route::post('/pending-changes/bulk-approve',                  [PendingChangeController::class, 'bulkApprove'])     ->name('pending-changes.bulk-approve');
        Route::post('/pending-changes/bulk-reject',                   [PendingChangeController::class, 'bulkReject'])      ->name('pending-changes.bulk-reject');
        // Config tables — admin only (index visible to all auth users below)
    });

    Route::get('/my-requests', [PendingChangeController::class, 'myRequests'])->name('pending-changes.my');
    Route::get('/pending-changes/{pendingChange}/edit-request',    [PendingChangeController::class, 'editRequest'])   ->name('pending-changes.edit-request');
    Route::patch('/pending-changes/{pendingChange}/update-request', [PendingChangeController::class, 'updateRequest']) ->name('pending-changes.update-request');
    Route::delete('/pending-changes/{pendingChange}/withdraw',      [PendingChangeController::class, 'withdraw'])      ->name('pending-changes.withdraw');

    // Config tables — all authenticated users (write operations go through pending changes for non-admins)
    Route::resource('marital-statuses',      MaritalStatusController::class)     ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('associations',          AssociationController::class)        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('verification-statuses', VerificationStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('final-statuses',        \App\Http\Controllers\FinalStatusController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/members/national-ids',              [MemberController::class, 'nationalIdsIndex'])      ->name('members.national-ids');
    Route::get('/members/national-ids/export',       [MemberController::class, 'nationalIdsExport'])     ->name('members.national-ids.export');
    Route::post('/members/national-ids/import',      [MemberController::class, 'nationalIdsImportStore'])->name('members.national-ids.import');
    Route::patch('/members/{member}/national-id',    [MemberController::class, 'updateNationalId'])      ->name('members.national-id.update');
    Route::get('/members/map',                       [MemberController::class, 'mapIndex'])           ->name('members.map');
    Route::get('/members/export',                    [MemberController::class, 'export'])              ->name('members.export');
    Route::get('/members/custom-export',             [CustomExportController::class, 'show'])          ->name('members.custom-export');
    Route::post('/members/custom-export/download',   [CustomExportController::class, 'download'])      ->name('members.custom-export.download');
    Route::delete('/members/bulk-destroy',          [MemberController::class, 'bulkDestroy'])        ->name('members.bulk-destroy');
    Route::patch('/members/bulk-update',            [MemberController::class, 'bulkUpdate'])          ->name('members.bulk-update');
    Route::patch('/members/{member}/final-status', [MemberController::class, 'updateFinalStatus'])->name('members.final-status.update');
    Route::get('/members/bulk-amount',      [MemberController::class, 'bulkAmountShow'])      ->name('members.bulk-amount');
    Route::post('/members/bulk-amount',     [MemberController::class, 'bulkAmountApply'])     ->name('members.bulk-amount.apply');
    Route::get('/members/fv-reduction',    [MemberController::class, 'fvReductionShow'])     ->name('members.fv-reduction');
    Route::post('/members/fv-reduction',   [MemberController::class, 'fvReductionApply'])    ->name('members.fv-reduction.apply');
    Route::get('/members/bulk-payments',         [MemberController::class, 'bulkPaymentsShow'])    ->name('members.bulk-payments');
    Route::post('/members/bulk-payments',        [MemberController::class, 'bulkPaymentsApply'])   ->name('members.bulk-payments.apply');
    Route::get('/members/payment-batches',          [MemberController::class, 'paymentBatchesIndex'])  ->name('members.payment-batches');
    Route::get('/members/payment-batches/{batch}',  [MemberController::class, 'paymentBatchShow'])     ->name('members.payment-batches.show');
    Route::patch('/members/payment-batches/{batch}',[MemberController::class, 'updatePaymentBatch'])   ->name('members.payment-batches.update');
    Route::get('/members/score-deductions',  [MemberController::class, 'scoreDeductionsIndex']) ->name('members.score-deductions');
    Route::get('/members/score-additions',   [MemberController::class, 'scoreAdditionsIndex'])  ->name('members.score-additions');
    Route::get('/members/score-adjustments',  [MemberController::class, 'scoreAdjustmentsIndex']) ->name('members.score-adjustments');
    Route::post('/members/bulk-set-score',         [MemberController::class, 'bulkSetScoreAdjustment']) ->name('members.bulk-set-score');
    Route::post('/members/bulk-recalculate-score', [MemberController::class, 'bulkRecalculateScore'])   ->name('members.bulk-recalculate-score');
    Route::get('/members/score-equalizer',    [MemberController::class, 'scoreEqualizerIndex'])   ->name('members.score-equalizer');
    Route::post('/members/score-equalizer',   [MemberController::class, 'scoreEqualizerApply'])   ->name('members.score-equalizer.apply');
    Route::get('/members/score-manager',        [MemberController::class, 'scoreManagerIndex'])  ->name('members.score-manager');
    Route::get('/members/score-manager/export',       [MemberController::class, 'scoreManagerExport'])    ->name('members.score-manager.export');
    Route::post('/members/bulk-score-update',          [MemberController::class, 'bulkScoreUpdate'])        ->name('members.bulk-score-update');
    Route::patch('/members/{member}/score',   [MemberController::class, 'updateMemberScore'])     ->name('members.score.update');
    Route::delete('/members/{member}/score',  [MemberController::class, 'resetMemberScore'])      ->name('members.score.reset');
    Route::get('/members/import',                 [MemberImportController::class, 'show'])    ->name('members.import.show');
    Route::post('/members/import',                [MemberImportController::class, 'store'])   ->name('members.import.store');
    Route::get('/members/import-gender',          [\App\Http\Controllers\GenderImportController::class, 'show'])  ->name('members.import-gender.show');
    Route::post('/members/import-gender',         [\App\Http\Controllers\GenderImportController::class, 'store']) ->name('members.import-gender.store');
    Route::get('/members/import-region',          [\App\Http\Controllers\RegionImportController::class, 'show'])  ->name('members.import-region.show');
    Route::post('/members/import-region',         [\App\Http\Controllers\RegionImportController::class, 'store']) ->name('members.import-region.store');
    Route::get('/members/import/template', [MemberImportController::class, 'template'])->name('members.import.template');
    Route::get('/members/import/{importResult}/status',        [MemberImportController::class, 'status'])->name('members.import.status');
    Route::post('/members/import/{importResult}/chunk',        [MemberImportController::class, 'chunk']) ->name('members.import.chunk');
    Route::resource('members', MemberController::class);
    Route::patch('/members/{member}/address',  [MemberController::class, 'updateAddress'])  ->name('members.address.update');
    Route::patch('/members/{member}/region',   [MemberController::class, 'updateRegion'])   ->name('members.region.update');
    Route::patch('/members/{member}/sector',   [MemberController::class, 'updateSector'])   ->name('members.sector.update');
    Route::patch('/members/{member}/location', [MemberController::class, 'updateLocation']) ->name('members.location.update');
    Route::get('/member-images',                     [MemberImageController::class, 'index'])      ->name('member-images.index');
    Route::post('/member-images',                    [MemberImageController::class, 'storeGlobal'])->name('member-images.store-global');
    Route::get('/member-images/{memberImage}/edit',  [MemberImageController::class, 'edit'])       ->name('member-images.edit');
    Route::patch('/member-images/{memberImage}',     [MemberImageController::class, 'update'])     ->name('member-images.update');
    Route::post('/members/{member}/images',          [MemberImageController::class, 'store'])      ->name('member-images.store');
    Route::delete('/member-images/{memberImage}',    [MemberImageController::class, 'destroy'])    ->name('member-images.destroy');
    Route::get('/members-duplicates', [DuplicateMembersController::class, 'index'])->name('members.duplicates');
    Route::get('/age-statistics',     [\App\Http\Controllers\AgeStatisticsController::class, 'index'])->name('age-statistics.index');
    Route::get('/delegates',              [DelegateController::class, 'index'])      ->name('delegates.index');
    Route::post('/delegates',             [DelegateController::class, 'store'])      ->name('delegates.store');
    Route::post('/delegates/quick-store', [DelegateController::class, 'quickStore']) ->name('delegates.quick-store');
    Route::get('/delegates/{delegate}',   [DelegateController::class, 'show'])    ->name('delegates.show');
    Route::patch('/delegates/{delegate}', [DelegateController::class, 'rename'])  ->name('delegates.rename');
    Route::delete('/delegates/{delegate}',[DelegateController::class, 'destroy']) ->name('delegates.destroy');
    Route::resource('field-visit-statuses', \App\Http\Controllers\FieldVisitStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('house-types',      \App\Http\Controllers\HouseTypeController::class)     ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('house-conditions', \App\Http\Controllers\HouseConditionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('housing-statuses', \App\Http\Controllers\HousingStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('employees', EmployeeController::class)->except(['create', 'edit']);
    Route::post('/employees/{employee}/transactions',                 [EmployeeController::class, 'storeTransaction'])   ->name('employees.transactions.store');
    Route::put('/employees/{employee}/transactions/{transaction}',   [EmployeeController::class, 'updateTransaction'])  ->name('employees.transactions.update');
    Route::delete('/employees/{employee}/transactions/{transaction}', [EmployeeController::class, 'destroyTransaction']) ->name('employees.transactions.destroy');

    Route::resource('regions', \App\Http\Controllers\RegionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/regions/quick-store', [\App\Http\Controllers\RegionController::class, 'quickStore'])->name('regions.quick-store');
    Route::post('/sectors/quick-store', [\App\Http\Controllers\SectorController::class, 'quickStore'])->name('sectors.quick-store');
    Route::get('/sectors/export/all',       [\App\Http\Controllers\SectorController::class, 'export'])       ->name('sectors.export');
    Route::get('/sectors/{sector}/export',  [\App\Http\Controllers\SectorController::class, 'exportSingle']) ->name('sectors.export-single');
    Route::resource('sectors', \App\Http\Controllers\SectorController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::post('/sectors/{sector}/regions', [\App\Http\Controllers\SectorController::class, 'updateRegions'])->name('sectors.update-regions');
    Route::get('/field-visits/with-amounts',                               [\App\Http\Controllers\FieldVisitController::class, 'withAmounts'])->name('field-visits.with-amounts');
    Route::post('/members/{member}/field-visits',                          [\App\Http\Controllers\FieldVisitController::class, 'store'])  ->name('field-visits.store');
    Route::put('/members/{member}/field-visits/{fieldVisit}',             [\App\Http\Controllers\FieldVisitController::class, 'update'])        ->name('field-visits.update');
    Route::patch('/members/{member}/field-visits/{fieldVisit}/adjust',   [\App\Http\Controllers\FieldVisitController::class, 'adjustAmount'])  ->name('field-visits.adjust');
    Route::delete('/members/{member}/field-visits/{fieldVisit}',          [\App\Http\Controllers\FieldVisitController::class, 'destroy'])->name('field-visits.destroy');
    // Donations — named routes before resource to avoid conflict with {donation}
    Route::get('/donations/monthly',       [DonationController::class, 'monthly'])     ->name('donations.monthly');
    Route::post('/donations/monthly/quick',[DonationController::class, 'quickDonate'])->name('donations.monthly.quick');
    Route::resource('donations', DonationController::class)->except(['show']);
});

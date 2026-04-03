<?php

namespace App\Http\Controllers;

use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\PendingChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $myPendingCount  = PendingChange::where('requested_by', Auth::id())->where('status', 'pending')->count();
        $myRejectedCount = PendingChange::where('requested_by', Auth::id())->where('status', 'rejected')->count();
        $myRecentChanges = PendingChange::with('reviewer')
                            ->where('requested_by', Auth::id())
                            ->latest()->limit(5)->get();

        $allPendingCount = Auth::user()?->role === 'admin'
            ? PendingChange::where('status', 'pending')->count()
            : 0;

        $finalStatuses       = FinalStatus::active()->withCount('members')->orderBy('name')->get();
        $noFinalStatusCount  = Member::whereNull('final_status_id')->count();

        return view('dashboard', compact(
            'myPendingCount', 'myRejectedCount', 'myRecentChanges',
            'allPendingCount', 'finalStatuses', 'noFinalStatusCount'
        ));
    }
}

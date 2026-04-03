<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingChangeController extends Controller
{
    public function myRequests(Request $request)
    {
        $status  = $request->get('status', '');
        $query   = PendingChange::with('reviewer')
                    ->where('requested_by', Auth::id())
                    ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $changes        = $query->paginate(20)->withQueryString();
        $rejectedCount  = PendingChange::where('requested_by', Auth::id())->where('status', 'rejected')->count();
        $pendingCount   = PendingChange::where('requested_by', Auth::id())->where('status', 'pending')->count();

        return view('pending-changes.my', compact('changes', 'status', 'rejectedCount', 'pendingCount'));
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $type   = $request->get('type');

        $query = PendingChange::with('requester', 'reviewer')->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('model_type', $type)->where('action', $type === 'delete' ? 'delete' : $query->getModel()->getTable());
        }

        $changes       = $query->paginate(20)->withQueryString();
        $pendingCount  = PendingChange::where('status', 'pending')->count();

        return view('pending-changes.index', compact('changes', 'status', 'pendingCount'));
    }

    public function show(PendingChange $pendingChange)
    {
        $pendingChange->load('requester', 'reviewer');
        return view('pending-changes.show', compact('pendingChange'));
    }

    public function approve(PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'هذا الطلب تمت معالجته مسبقاً.');

        try {
            $pendingChange->apply();
            ActivityLogger::log('approved', "الموافقة على طلب: {$pendingChange->summary()}");
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء تطبيق التعديل: ' . $e->getMessage());
        }

        return redirect()->route('pending-changes.index')
                         ->with('success', 'تمت الموافقة على الطلب وتطبيق التعديل.');
    }

    public function reject(Request $request, PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'هذا الطلب تمت معالجته مسبقاً.');

        $request->validate(['reviewer_notes' => 'nullable|string|max:1000']);

        $pendingChange->cleanup();

        $pendingChange->update([
            'status'         => 'rejected',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'reviewer_notes' => $request->input('reviewer_notes'),
        ]);

        ActivityLogger::log('rejected', "رفض طلب: {$pendingChange->summary()}");

        return redirect()->route('pending-changes.index')
                         ->with('success', 'تم رفض الطلب.');
    }
}

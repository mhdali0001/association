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

        $orderBy = ($status === 'rejected' || $status === 'approved') ? 'reviewed_at' : 'created_at';
        $query = PendingChange::with('requester', 'reviewer')->orderBy($orderBy, 'desc');

        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('model_type', $type)->where('action', $type === 'delete' ? 'delete' : $query->getModel()->getTable());
        }

        $changes       = $query->paginate(20)->withQueryString();
        $pendingCount  = PendingChange::where('status', 'pending')->count();

        // Resolve member_id per change
        $visitIds = [];
        $memberIds = [];
        foreach ($changes as $c) {
            if ($c->model_type === 'member') {
                if ($c->model_id) $memberIds[] = $c->model_id;
            } else {
                $mid = $c->payload['member_id'] ?? $c->getAttribute('original')['member_id'] ?? null;
                if ($mid) {
                    $memberIds[] = $mid;
                } elseif ($c->model_type === 'field_visit' && $c->model_id) {
                    $visitIds[] = $c->model_id;
                }
            }
        }

        // For field_visits without member_id in payload, resolve via DB
        if (!empty($visitIds)) {
            $visitMemberIds = \App\Models\FieldVisit::whereIn('id', array_unique($visitIds))
                ->pluck('member_id', 'id')->toArray(); // visitId => memberId
            $memberIds = array_merge($memberIds, array_values($visitMemberIds));
        } else {
            $visitMemberIds = [];
        }

        $dossierMap = \App\Models\Member::whereIn('id', array_unique(array_filter($memberIds)))
            ->pluck('dossier_number', 'id')
            ->toArray();

        // Build visitId => dossier map for fallback
        $visitDossierMap = [];
        foreach ($visitMemberIds as $visitId => $memberId) {
            if (isset($dossierMap[$memberId])) {
                $visitDossierMap[$visitId] = $dossierMap[$memberId];
            }
        }

        return view('pending-changes.index', compact('changes', 'status', 'pendingCount', 'dossierMap', 'visitDossierMap'));
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

    public function approveWithEdit(Request $request, PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'هذا الطلب تمت معالجته مسبقاً.');

        $request->validate(['reviewer_notes' => 'nullable|string|max:1000']);

        // Merge edited fields into payload
        $edited  = $request->input('payload', []);
        $payload = $pendingChange->payload ?? [];

        // Deep merge: only update keys that were submitted
        foreach ($edited as $key => $value) {
            $payload[$key] = $value === '' ? null : $value;
        }

        // Handle nested scores if submitted
        if ($request->has('scores')) {
            $payload['scores'] = array_merge($payload['scores'] ?? [], $request->input('scores', []));
        }

        $pendingChange->update([
            'payload'        => $payload,
            'reviewer_notes' => $request->input('reviewer_notes'),
        ]);

        try {
            $pendingChange->apply();
            ActivityLogger::log('approved', "الموافقة (مع تعديل) على طلب: {$pendingChange->summary()}");
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء تطبيق التعديل: ' . $e->getMessage());
        }

        return redirect()->route('pending-changes.index')
                         ->with('success', 'تمت الموافقة على الطلب مع التعديلات وتطبيقه.');
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

    public function reopen(PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isRejected(), 422, 'يمكن إعادة الفتح للطلبات المرفوضة فقط.');

        $pendingChange->update([
            'status'         => 'pending',
            'reviewed_by'    => null,
            'reviewed_at'    => null,
            'reviewer_notes' => null,
        ]);

        ActivityLogger::log('updated', "إعادة فتح طلب مرفوض: {$pendingChange->summary()}");

        return redirect()->route('pending-changes.show', $pendingChange)
                         ->with('success', 'تمت إعادة فتح الطلب — أصبح بانتظار المراجعة مجدداً.');
    }

    public function revoke(Request $request, PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isApproved(), 422, 'يمكن إعادة الرفض للطلبات الموافق عليها فقط.');

        $request->validate(['reviewer_notes' => 'nullable|string|max:1000']);

        try {
            $pendingChange->undo();
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء التراجع عن التعديل: ' . $e->getMessage());
        }

        $pendingChange->update([
            'status'         => 'rejected',
            'reviewed_by'    => Auth::id(),
            'reviewed_at'    => now(),
            'reviewer_notes' => $request->input('reviewer_notes'),
        ]);

        ActivityLogger::log('rejected', "إعادة رفض وتراجع عن طلب موافق عليه: {$pendingChange->summary()}");

        return redirect()->route('pending-changes.show', $pendingChange)
                         ->with('success', 'تم إعادة رفض الطلب.');
    }
}

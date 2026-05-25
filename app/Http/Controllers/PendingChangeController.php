<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use App\Models\User;
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
        $status      = $request->get('status', 'pending');
        $type        = $request->get('type');
        $modelType   = trim($request->get('model_type', ''));
        $dateFrom    = trim($request->get('date_from', ''));
        $dateTo      = trim($request->get('date_to', ''));
        $requestedBy = trim($request->get('requested_by', ''));
        $visitorName = trim($request->get('visitor_name', ''));

        $orderBy = ($status === 'rejected' || $status === 'approved') ? 'reviewed_at' : 'created_at';
        $query = PendingChange::with('requester', 'reviewer')->orderBy($orderBy, 'desc');

        if ($status)         $query->where('status', $status);
        if ($type)           $query->where('model_type', $type)->where('action', $type === 'delete' ? 'delete' : $query->getModel()->getTable());
        if ($modelType   !== '') $query->where('model_type', $modelType);
        if ($requestedBy !== '') $query->where('requested_by', $requestedBy);
        if ($dateFrom    !== '') $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo      !== '') $query->whereDate('created_at', '<=', $dateTo);
        if ($visitorName !== '') $query->where('payload->visitor', 'like', "%{$visitorName}%");

        $changes = $query->paginate(20)->withQueryString();

        // Base query for counts — applies same filters except status
        $hasFilters = $modelType !== '' || $requestedBy !== '' || $dateFrom !== '' || $dateTo !== '' || $visitorName !== '';
        if ($hasFilters) {
            $countBase = PendingChange::query();
            if ($type)           $countBase->where('model_type', $type);
            if ($modelType   !== '') $countBase->where('model_type', $modelType);
            if ($requestedBy !== '') $countBase->where('requested_by', $requestedBy);
            if ($dateFrom    !== '') $countBase->whereDate('created_at', '>=', $dateFrom);
            if ($dateTo      !== '') $countBase->whereDate('created_at', '<=', $dateTo);
            if ($visitorName !== '') $countBase->where('payload->visitor', 'like', "%{$visitorName}%");
            $pendingCount  = (clone $countBase)->where('status', 'pending')->count();
            $approvedCount = (clone $countBase)->where('status', 'approved')->count();
            $rejectedCount = (clone $countBase)->where('status', 'rejected')->count();
        } else {
            $pendingCount  = PendingChange::where('status', 'pending')->count();
            $approvedCount = PendingChange::where('status', 'approved')->count();
            $rejectedCount = PendingChange::where('status', 'rejected')->count();
        }
        $totalCount = $pendingCount + $approvedCount + $rejectedCount;
        $usersList  = User::orderBy('name')->get(['id', 'name']);

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

        return view('pending-changes.index', compact('changes', 'status', 'pendingCount', 'approvedCount', 'rejectedCount', 'totalCount', 'dossierMap', 'visitDossierMap', 'dateFrom', 'dateTo', 'modelType', 'requestedBy', 'visitorName', 'usersList'));
    }

    public function show(PendingChange $pendingChange)
    {
        $pendingChange->load('requester', 'reviewer');

        $memberSnapshots = null;
        if (str_starts_with($pendingChange->action, 'bulk_')) {
            $memberSnapshots = $pendingChange->memberSnapshots()
                ->orderBy('full_name')
                ->paginate(30, ['*'], 'mpage');
        }

        return view('pending-changes.show', compact('pendingChange', 'memberSnapshots'));
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

    public function editRequest(PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'يمكن تعديل الطلبات المعلّقة فقط.');
        abort_unless($pendingChange->requested_by === Auth::id(), 403);

        $pendingChange->load('requester');
        return view('pending-changes.edit-request', compact('pendingChange'));
    }

    public function updateRequest(Request $request, PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'يمكن تعديل الطلبات المعلّقة فقط.');
        abort_unless($pendingChange->requested_by === Auth::id(), 403);

        $request->validate(['_requester_notes' => 'nullable|string|max:500']);

        $payload = $pendingChange->payload ?? [];

        // Requester note
        $payload['_requester_notes'] = $request->input('_requester_notes') ?: null;

        // Merge all submitted payload fields (deep merge for nested arrays like scores/payment)
        $edited = $request->input('payload', []);

        // Admin-only fields the requester cannot change
        $adminOnly = ['sham_cash_account'];

        foreach ($edited as $key => $value) {
            if (in_array($key, $adminOnly)) continue;
            if (is_array($value)) {
                $existing = $payload[$key] ?? [];
                foreach ($value as $subKey => $subVal) {
                    $existing[$subKey] = ($subVal === '' || $subVal === null) ? null : $subVal;
                }
                $payload[$key] = $existing;
            } else {
                $payload[$key] = ($value === '' || $value === null) ? null : $value;
            }
        }

        $pendingChange->update(['payload' => $payload]);
        ActivityLogger::log('updated', "تعديل طلب معلّق: {$pendingChange->summary()}");

        return redirect()->route('pending-changes.my', ['status' => 'pending'])
            ->with('success', 'تم تحديث الطلب بنجاح — لا يزال بانتظار موافقة المسؤول.');
    }

    public function withdraw(PendingChange $pendingChange)
    {
        abort_unless($pendingChange->isPending(), 422, 'يمكن سحب الطلبات المعلّقة فقط.');
        abort_unless($pendingChange->requested_by === Auth::id(), 403);

        $summary = $pendingChange->summary();
        $pendingChange->cleanup();
        $pendingChange->delete();

        ActivityLogger::log('deleted', "سحب طلب معلّق: {$summary}");

        return redirect()->route('pending-changes.my')
            ->with('success', 'تم سحب الطلب وإلغاؤه بنجاح.');
    }

    public function bulkApprove(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي طلب.');
        }

        $changes = PendingChange::whereIn('id', $ids)->where('status', 'pending')->get();
        $done = 0;
        $errors = [];

        foreach ($changes as $change) {
            try {
                $change->apply();
                $done++;
            } catch (\Throwable $e) {
                $errors[] = "#{$change->id}: " . $e->getMessage();
            }
        }

        ActivityLogger::log('approved', "موافقة جماعية على {$done} طلب");

        $msg = "تمت الموافقة على {$done} طلب بنجاح.";
        if (!empty($errors)) {
            $msg .= ' فشل: ' . implode('، ', $errors);
        }

        return back()->with($errors ? 'error' : 'success', $msg);
    }

    public function bulkReject(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي طلب.');
        }

        $count = PendingChange::whereIn('id', $ids)->where('status', 'pending')->each(function ($change) {
            $change->cleanup();
            $change->update([
                'status'      => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        })->count();

        ActivityLogger::log('rejected', "رفض جماعي لـ {$count} طلب");

        return back()->with('success', "تم رفض {$count} طلب بنجاح.");
    }
}

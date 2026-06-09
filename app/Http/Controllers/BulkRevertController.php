<?php

namespace App\Http\Controllers;

use App\Models\BulkRevertSession;
use App\Services\BulkRevertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BulkRevertController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $sessions = BulkRevertSession::with(['user', 'revertedByUser'])
            ->latest()
            ->paginate(30);

        return view('bulk-revert.index', compact('sessions'));
    }

    public function revert(Request $request, BulkRevertSession $session)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        if ($session->isReverted()) {
            $msg = 'تم التراجع عن هذه الجلسة مسبقاً.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $count = BulkRevertService::revert($session);

        $msg = "تم التراجع بنجاح عن العملية الجماعية ({$count} مستفيد).";

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg, 'count' => $count])
            : back()->with('success', $msg);
    }
}

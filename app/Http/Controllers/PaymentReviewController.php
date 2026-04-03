<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\PaymentInfo;
use App\Models\PaymentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $search = trim($request->get('search', ''));

        // Load all members that have at least one payment record (either table)
        $query = Member::query()
            ->with(['paymentInfo', 'paymentInfoAI', 'paymentReview.reviewer'])
            ->where(function ($q) {
                $q->whereHas('paymentInfo', fn($s) => $s->where(function ($x) {
                    $x->whereNotNull('iban')->orWhereNotNull('barcode');
                }))
                ->orWhereHas('paymentInfoAI', fn($s) => $s->where(function ($x) {
                    $x->whereNotNull('iban')->orWhereNotNull('barcode');
                }));
            })
            ->orderBy('full_name');

        if ($search) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $members = $query->get();

        // Annotate each member with auto-match result
        $members = $members->map(function ($member) {
            $pi  = $member->paymentInfo;
            $ai  = $member->paymentInfoAI;
            $member->auto_match = $pi && $ai
                && trim($pi->iban ?? '') !== ''
                && trim($ai->iban ?? '') !== ''
                && trim($pi->iban) === trim($ai->iban);
            return $member;
        });

        // Apply filter
        $members = match ($filter) {
            'pending'  => $members->filter(fn($m) => !$m->paymentReview || $m->paymentReview->isPending()),
            'reviewed' => $members->filter(fn($m) => $m->paymentReview && !$m->paymentReview->isPending()),
            'match'    => $members->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMatch()),
            'mismatch' => $members->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMismatch()),
            default    => $members,
        };

        // Stats (before filter)
        $allMembers    = $query->get();
        $totalCount    = $allMembers->count();
        $pendingCount  = $allMembers->filter(fn($m) => !$m->paymentReview || $m->paymentReview->isPending())->count();
        $reviewedCount = $allMembers->filter(fn($m) => $m->paymentReview && !$m->paymentReview->isPending())->count();
        $matchCount    = $allMembers->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMatch())->count();
        $mismatchCount = $allMembers->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMismatch())->count();

        return view('payment-review.index', compact(
            'members', 'filter', 'search',
            'totalCount', 'pendingCount', 'reviewedCount', 'matchCount', 'mismatchCount'
        ));
    }

    public function duplicateIbans(Request $request)
    {
        $search = trim($request->get('search', ''));

        // Find IBANs that appear more than once in payment_info
        $duplicateIbans = DB::table('payment_info')
            ->select('iban', DB::raw('COUNT(*) as count'))
            ->whereNotNull('iban')
            ->where('iban', '!=', '')
            ->groupBy('iban')
            ->having('count', '>', 1)
            ->orderByDesc('count')
            ->pluck('count', 'iban');

        // Load all members that have those IBANs, with full info
        $membersByIban = collect();

        foreach ($duplicateIbans as $iban => $count) {
            if ($search && stripos($iban, $search) === false) {
                // also check member names
                $members = Member::whereHas('paymentInfo', fn($q) => $q->where('iban', $iban))
                    ->where('full_name', 'like', "%{$search}%")
                    ->with(['paymentInfo', 'verificationStatus', 'finalStatus', 'association'])
                    ->orderBy('full_name')
                    ->get();
                if ($members->isEmpty()) continue;
            } else {
                $members = Member::whereHas('paymentInfo', fn($q) => $q->where('iban', $iban))
                    ->with(['paymentInfo', 'verificationStatus', 'finalStatus', 'association'])
                    ->orderBy('full_name')
                    ->get();
            }

            $membersByIban[$iban] = $members;
        }

        $totalDuplicateIbans   = $membersByIban->count();
        $totalAffectedMembers  = $membersByIban->flatten()->count();

        return view('payment-review.duplicate-ibans', compact(
            'membersByIban', 'search', 'totalDuplicateIbans', 'totalAffectedMembers'
        ));
    }

    public function store(Request $request, Member $member)
    {
        $data = $request->validate([
            'status' => 'required|in:match,mismatch,pending',
            'notes'  => 'nullable|string|max:500',
        ]);

        PaymentReview::updateOrCreate(
            ['member_id' => $member->id],
            [
                'status'      => $data['status'],
                'notes'       => $data['notes'] ?? null,
                'reviewed_by' => $data['status'] === 'pending' ? null : Auth::id(),
                'reviewed_at' => $data['status'] === 'pending' ? null : now(),
            ]
        );

        return back()->with('success', "تم حفظ نتيجة المراجعة لـ {$member->full_name}.");
    }
}

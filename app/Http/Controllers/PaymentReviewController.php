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

        // Annotate all members with auto-match result
        $allAnnotated = $query->get()->map(function ($member) {
            $pi     = $member->paymentInfo;
            $ai     = $member->paymentInfoAI;
            $piIban = str_replace(' ', '', $pi->iban ?? '');
            $aiIban = str_replace(' ', '', $ai->iban ?? '');
            $member->auto_match = $pi && $ai
                && $piIban !== ''
                && $aiIban !== ''
                && $piIban === $aiIban;
            return $member;
        });

        // Stats (before filter)
        $totalCount         = $allAnnotated->count();
        $pendingCount       = $allAnnotated->filter(fn($m) => !$m->paymentReview || $m->paymentReview->isPending())->count();
        $reviewedCount      = $allAnnotated->filter(fn($m) => $m->paymentReview && !$m->paymentReview->isPending())->count();
        $matchCount         = $allAnnotated->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMatch())->count();
        $mismatchCount      = $allAnnotated->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMismatch())->count();
        $autoMatchCount     = $allAnnotated->filter(fn($m) => $m->auto_match)->count();
        $autoMismatchCount  = $allAnnotated->filter(fn($m) => !$m->auto_match)->count();

        // Apply filter
        $members = match ($filter) {
            'pending'       => $allAnnotated->filter(fn($m) => !$m->paymentReview || $m->paymentReview->isPending()),
            'reviewed'      => $allAnnotated->filter(fn($m) => $m->paymentReview && !$m->paymentReview->isPending()),
            'match'         => $allAnnotated->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMatch()),
            'mismatch'      => $allAnnotated->filter(fn($m) => $m->paymentReview && $m->paymentReview->isMismatch()),
            'auto_match'    => $allAnnotated->filter(fn($m) => $m->auto_match),
            'auto_mismatch' => $allAnnotated->filter(fn($m) => !$m->auto_match),
            default         => $allAnnotated,
        };

        return view('payment-review.index', compact(
            'members', 'filter', 'search',
            'totalCount', 'pendingCount', 'reviewedCount', 'matchCount', 'mismatchCount',
            'autoMatchCount', 'autoMismatchCount'
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

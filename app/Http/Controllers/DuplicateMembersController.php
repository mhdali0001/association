<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\VerificationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateMembersController extends Controller
{
    public function index(Request $request)
    {
        $type           = $request->get('type', 'all');   // all | national_id | phone | name
        $verificationId = $request->get('verification_status_id');
        $search         = trim($request->get('search', ''));

        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();

        // ── Helper: apply common filters to a query ───────────────
        $baseQuery = function () use ($verificationId, $search) {
            $q = Member::with('verificationStatus');
            if ($verificationId) {
                $q->where('verification_status_id', $verificationId);
            }
            if ($search !== '') {
                $q->where(function ($q2) use ($search) {
                    $q2->where('full_name',   'like', "%{$search}%")
                       ->orWhere('national_id', 'like', "%{$search}%")
                       ->orWhere('phone',        'like', "%{$search}%");
                });
            }
            return $q;
        };

        // ── Duplicates by national_id ─────────────────────────────
        $byNationalId = collect();
        if (in_array($type, ['all', 'national_id'])) {
            $dupNidValues = DB::table('members')
                ->select('national_id')
                ->whereNotNull('national_id')
                ->where('national_id', '!=', '')
                ->groupBy('national_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('national_id');

            $byNationalId = $baseQuery()
                ->whereIn('national_id', $dupNidValues)
                ->orderBy('national_id')->orderBy('id')
                ->get()
                ->groupBy('national_id')
                ->filter(fn($g) => $g->count() > 1);
        }

        // ── Duplicates by phone ───────────────────────────────────
        $byPhone = collect();
        if (in_array($type, ['all', 'phone'])) {
            $dupPhoneValues = DB::table('members')
                ->select('phone')
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->groupBy('phone')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('phone');

            $byPhone = $baseQuery()
                ->whereIn('phone', $dupPhoneValues)
                ->orderBy('phone')->orderBy('id')
                ->get()
                ->groupBy('phone')
                ->filter(fn($g) => $g->count() > 1);
        }

        // ── Duplicates by full_name ───────────────────────────────
        $byName = collect();
        if (in_array($type, ['all', 'name'])) {
            $dupNameValues = DB::table('members')
                ->select('full_name')
                ->whereNotNull('full_name')
                ->groupBy('full_name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('full_name');

            $byName = $baseQuery()
                ->whereIn('full_name', $dupNameValues)
                ->orderBy('full_name')->orderBy('id')
                ->get()
                ->groupBy('full_name')
                ->filter(fn($g) => $g->count() > 1);
        }

        $totalGroups = $byNationalId->count() + $byPhone->count() + $byName->count();

        return view('members.duplicates', compact(
            'byNationalId', 'byPhone', 'byName', 'totalGroups',
            'type', 'verificationId', 'search', 'verificationStatuses'
        ));
    }
}

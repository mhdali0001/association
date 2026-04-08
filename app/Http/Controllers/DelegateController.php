<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DelegateController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        $delegates = DB::table('members')
            ->select('delegate', DB::raw('COUNT(*) as members_count'))
            ->whereNotNull('delegate')
            ->where('delegate', '!=', '')
            ->when($search, fn($q) => $q->where('delegate', 'like', "%{$search}%"))
            ->groupBy('delegate')
            ->orderByDesc('members_count')
            ->get();

        $totalDelegates = $delegates->count();
        $totalMembers   = $delegates->sum('members_count');

        return view('delegates.index', compact('delegates', 'search', 'totalDelegates', 'totalMembers'));
    }

    public function show(Request $request, string $delegate)
    {
        $members = Member::with(['verificationStatus', 'finalStatus'])
            ->where('delegate', $delegate)
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('delegates.show', compact('delegate', 'members'));
    }
}

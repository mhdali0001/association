<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function rename(Request $request, string $delegate)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $data = $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        $newName = trim($data['new_name']);
        $count   = Member::where('delegate', $delegate)->update(['delegate' => $newName]);

        return redirect()->route('delegates.index')
            ->with('success', "تم تغيير اسم المندوب إلى «{$newName}» وتحديث {$count} عضو.");
    }

    public function destroy(string $delegate)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $count = Member::where('delegate', $delegate)->update(['delegate' => null]);

        return redirect()->route('delegates.index')
            ->with('success', "تم إزالة المندوب «{$delegate}» من {$count} عضو.");
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

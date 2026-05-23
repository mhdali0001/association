<?php

namespace App\Http\Controllers;

use App\Models\Delegate;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DelegateController extends Controller
{
    use \App\Http\Controllers\Concerns\FiltersMembersQuery;

    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        // All delegate names from the delegates table
        $tableNames = Delegate::when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->pluck('name');

        // Member counts grouped by delegate name
        $memberCounts = DB::table('members')
            ->select('delegate', DB::raw('COUNT(*) as members_count'))
            ->whereNotNull('delegate')
            ->where('delegate', '!=', '')
            ->when($search, fn($q) => $q->where('delegate', 'like', "%{$search}%"))
            ->groupBy('delegate')
            ->get()
            ->keyBy('delegate');

        // Merge: all names from both sources (union)
        $allNames = $tableNames
            ->merge($memberCounts->keys())
            ->unique()
            ->values();

        $delegates = $allNames->map(fn($name) => (object)[
            'name'          => $name,
            'members_count' => $memberCounts->get($name)?->members_count ?? 0,
        ])->sortByDesc('members_count')->values();

        $totalDelegates = $delegates->count();
        $totalMembers   = $delegates->sum('members_count');

        return view('delegates.index', compact('delegates', 'search', 'totalDelegates', 'totalMembers'));
    }

    public function quickStore(Request $request)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:delegates,name',
        ], [
            'name.required' => 'اسم المندوب مطلوب',
            'name.unique'   => 'هذا المندوب موجود مسبقاً',
        ]);

        $name     = trim($data['name']);
        $delegate = Delegate::create(['name' => $name]);

        return response()->json(['name' => $delegate->name]);
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:delegates,name',
        ], [
            'name.required' => 'اسم المندوب مطلوب',
            'name.unique'   => 'هذا المندوب موجود مسبقاً',
            'name.max'      => 'الاسم طويل جداً',
        ]);

        $name = trim($data['name']);
        Delegate::create(['name' => $name]);

        return redirect()->route('delegates.index')
            ->with('success', "تم إضافة المندوب «{$name}» بنجاح.");
    }

    public function rename(Request $request, string $delegate)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $data    = $request->validate(['new_name' => 'required|string|max:255']);
        $newName = trim($data['new_name']);

        Member::where('delegate', $delegate)->update(['delegate' => $newName]);
        Delegate::where('name', $delegate)->update(['name' => $newName]);

        return redirect()->route('delegates.index')
            ->with('success', "تم تغيير اسم المندوب إلى «{$newName}».");
    }

    public function destroy(string $delegate)
    {
        abort_if(Auth::user()?->role !== 'admin', 403);

        $count = Member::where('delegate', $delegate)->update(['delegate' => null]);
        Delegate::where('name', $delegate)->delete();

        return redirect()->route('delegates.index')
            ->with('success', "تم حذف المندوب «{$delegate}» وإزالته من {$count} عضو.");
    }

    public function show(Request $request, string $delegate)
    {
        $members = $this->buildFilteredQuery($request)
            ->where('delegate', $delegate)
            ->with(['verificationStatus', 'finalStatus', 'region', 'sector'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $filterValues = $this->filterViewData($request);
        $filterLists  = $this->filterListData();

        return view('delegates.show', array_merge(
            compact('delegate', 'members'),
            $filterValues,
            $filterLists
        ));
    }
}

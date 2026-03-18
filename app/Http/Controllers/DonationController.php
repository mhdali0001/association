<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Member;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $month  = $request->get('month');
        $type   = $request->get('type');
        $status = $request->get('status');

        $query = Donation::with('member', 'user')
            ->latest('donation_month')
            ->latest('id');

        if ($search) {
            $query->whereHas('member', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
        }
        if ($month) {
            $d = Carbon::parse($month . '-01');
            $query->whereYear('donation_month', $d->year)
                  ->whereMonth('donation_month', $d->month);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $donations = $query->paginate(25)->withQueryString();
        $total     = (clone $query->getQuery())->sum('amount');

        return view('donations.index', compact('donations', 'total', 'search', 'month', 'type', 'status'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');

        $donatedIds = Donation::forMonth($monthDate->year, $monthDate->month)
            ->where('status', '!=', 'cancelled')
            ->pluck('member_id')
            ->unique();

        $allMembers = Member::orderBy('full_name')->get();

        $donatedMembers    = $allMembers->whereIn('id', $donatedIds);
        $notDonatedMembers = $allMembers->whereNotIn('id', $donatedIds);

        $monthDonations = Donation::with('member')
            ->forMonth($monthDate->year, $monthDate->month)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('member_id');

        return view('donations.monthly', compact(
            'donatedMembers', 'notDonatedMembers', 'monthDonations',
            'month', 'monthDate'
        ));
    }

    public function create()
    {
        $members = Member::orderBy('full_name')->get();
        return view('donations.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'        => 'required|exists:members,id',
            'amount'           => 'required|numeric|min:0',
            'donation_month'   => 'required|date_format:Y-m',
            'type'             => 'required|in:manual,sham_cash',
            'status'           => 'required|in:paid,pending,cancelled',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        $data['donation_month'] = Carbon::parse($data['donation_month'] . '-01')->toDateString();
        $data['user_id']        = Auth::id();

        $donation = Donation::create($data);
        ActivityLogger::log(
            'created',
            "تسجيل تبرع لـ {$donation->member->full_name} — {$donation->amount} ل.س ({$donation->donation_month->format('Y/m')})",
            $donation->member
        );

        return redirect()->route('donations.index')->with('success', 'تم تسجيل التبرع بنجاح.');
    }

    public function edit(Donation $donation)
    {
        $members = Member::orderBy('full_name')->get();
        return view('donations.edit', compact('donation', 'members'));
    }

    public function update(Request $request, Donation $donation)
    {
        $data = $request->validate([
            'member_id'        => 'required|exists:members,id',
            'amount'           => 'required|numeric|min:0',
            'donation_month'   => 'required|date_format:Y-m',
            'type'             => 'required|in:manual,sham_cash',
            'status'           => 'required|in:paid,pending,cancelled',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        $data['donation_month'] = Carbon::parse($data['donation_month'] . '-01')->toDateString();

        $donation->update($data);
        ActivityLogger::log(
            'updated',
            "تعديل تبرع لـ {$donation->member->full_name} — {$donation->amount} ل.س",
            $donation->member
        );

        return redirect()->route('donations.index')->with('success', 'تم تحديث التبرع بنجاح.');
    }

    public function destroy(Donation $donation)
    {
        $memberName = $donation->member->full_name;
        $month      = $donation->donation_month->format('Y/m');
        $donation->delete();
        ActivityLogger::log('deleted', "حذف تبرع لـ {$memberName} ({$month})");

        return back()->with('success', 'تم حذف التبرع بنجاح.');
    }
}

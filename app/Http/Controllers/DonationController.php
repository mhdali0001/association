<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Donation;
use App\Models\FinalStatus;
use App\Models\MaritalStatus;
use App\Models\Member;
use App\Models\PendingChange;
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
        $month               = $request->get('month', now()->format('Y-m'));
        $search              = trim($request->get('search', ''));
        $dossierFrom         = trim($request->get('dossier_from', ''));
        $dossierTo           = trim($request->get('dossier_to', ''));
        $finalStatusIds      = array_filter((array) $request->get('final_status_id', []));
        $genders             = array_filter((array) $request->get('gender', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $delegates           = array_filter((array) $request->get('delegate', []));
        $networks            = array_filter((array) $request->get('network', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $maritalStatuses     = array_filter((array) $request->get('marital_status', []));

        $monthDate = Carbon::parse($month . '-01');

        $donatedIds = Donation::forMonth($monthDate->year, $monthDate->month)
            ->where('status', '!=', 'cancelled')
            ->pluck('member_id')
            ->unique();

        $membersQuery = Member::whereHas('verificationStatus', fn($q) => $q->whereIn('name', ['تم', 'يدوي']))
            ->orderBy('full_name');

        if ($search)        $membersQuery->where('full_name', 'like', "%{$search}%");
        if ($dossierFrom !== '') $membersQuery->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo   !== '') $membersQuery->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);
        if (!empty($finalStatusIds))  $membersQuery->whereIn('final_status_id', $finalStatusIds);
        if (!empty($genders))         $membersQuery->whereIn('gender', $genders);
        if (!empty($associationIds))  $membersQuery->whereIn('association_id', $associationIds);
        if ($specialCases !== '')     $membersQuery->where('special_cases', (bool) $specialCases);
        if (!empty($specialDescriptions)) $membersQuery->whereIn('special_cases_description', $specialDescriptions);
        if (!empty($delegates))       $membersQuery->whereIn('delegate', $delegates);
        if (!empty($networks))        $membersQuery->whereIn('network', $networks);
        if (!empty($addresses))       $membersQuery->whereIn('current_address', $addresses);
        if (!empty($maritalStatuses)) $membersQuery->whereIn('marital_status', $maritalStatuses);

        $allMembers = $membersQuery->get();

        $donatedMembers    = $allMembers->whereIn('id', $donatedIds);
        $notDonatedMembers = $allMembers->whereNotIn('id', $donatedIds);
        $totalEstimated    = $allMembers->sum('estimated_amount');

        $monthDonations = Donation::with('member')
            ->forMonth($monthDate->year, $monthDate->month)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('member_id');

        $totalDonated    = $monthDonations->flatten()->sum('amount');
        $totalNotDonated = $notDonatedMembers->sum('estimated_amount');
        $totalGrand      = $totalDonated + $totalNotDonated;

        // Filter lists
        $finalStatusList        = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList      = MaritalStatus::active()->orderBy('id')->get();
        $associationList        = Association::active()->orderBy('name')->get();
        $delegateList           = Member::whereNotNull('delegate')->where('delegate', '!=', '')
                                        ->distinct()->orderBy('delegate')->pluck('delegate');
        $specialDescriptionList = Member::whereNotNull('special_cases_description')->where('special_cases_description', '!=', '')
                                        ->distinct()->orderBy('special_cases_description')->pluck('special_cases_description');
        $addressList            = Member::whereNotNull('current_address')->where('current_address', '!=', '')
                                        ->distinct()->orderBy('current_address')->pluck('current_address');

        return view('donations.monthly', compact(
            'donatedMembers', 'notDonatedMembers', 'monthDonations',
            'month', 'monthDate', 'search', 'dossierFrom', 'dossierTo', 'totalEstimated',
            'totalDonated', 'totalNotDonated', 'totalGrand',
            'finalStatusIds', 'genders', 'associationIds', 'specialCases', 'specialDescriptions',
            'delegates', 'networks', 'addresses', 'maritalStatuses',
            'finalStatusList', 'maritalStatusList', 'associationList', 'delegateList',
            'specialDescriptionList', 'addressList'
        ));
    }

    public function quickDonate(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'month'     => 'required|date_format:Y-m',
        ]);

        $member    = Member::findOrFail($request->member_id);
        $month     = $request->month;
        $monthDate = Carbon::parse($month . '-01');
        $amount    = (float) ($member->estimated_amount ?? 0);

        if ($amount <= 0) {
            return back()->with('error', "لا يوجد مبلغ مقدر للعضو {$member->full_name}، يرجى تسجيل التبرع يدوياً.");
        }

        $type = $member->sham_cash_account ? 'sham_cash' : 'manual';

        $data = [
            'member_id'      => $member->id,
            'amount'         => $amount,
            'donation_month' => $monthDate->toDateString(),
            'type'           => $type,
            'status'         => 'paid',
            'user_id'        => Auth::id(),
        ];

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'donation',
                'action'       => 'create',
                'payload'      => array_merge($data, ['member_name' => $member->full_name]),
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', "تم إرسال طلب تبرع لـ {$member->full_name} للمراجعة.");
        }

        Donation::create($data);
        ActivityLogger::log(
            'created',
            "تسجيل تبرع فوري لـ {$member->full_name} — " . number_format($amount) . " ل.س ({$monthDate->format('Y/m')})",
            $member
        );

        return back()->with('success', "تم تسجيل تبرع {$member->full_name} بمبلغ " . number_format($amount) . " ل.س.");
    }

    public function create()
    {
        $members = Member::orderBy('full_name')->get();
        return view('donations.create', compact('members'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
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

        if (!$this->isAdmin()) {
            $member = Member::find($data['member_id']);
            PendingChange::create([
                'model_type'   => 'donation',
                'action'       => 'create',
                'payload'      => array_merge($data, ['member_name' => $member?->full_name]),
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('donations.index')
                             ->with('success', 'تم إرسال طلب إضافة التبرع للمراجعة، وسيُطبَّق بعد موافقة الأدمن.');
        }

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

        if (!$this->isAdmin()) {
            $member = Member::find($data['member_id']);
            PendingChange::create([
                'model_type'   => 'donation',
                'model_id'     => $donation->id,
                'action'       => 'update',
                'payload'      => array_merge($data, ['member_name' => $member?->full_name]),
                'original'     => array_merge($donation->toArray(), ['member_name' => $donation->member?->full_name]),
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('donations.index')
                             ->with('success', 'تم إرسال طلب تعديل التبرع للمراجعة، وسيُطبَّق بعد موافقة الأدمن.');
        }

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
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'donation',
                'model_id'     => $donation->id,
                'action'       => 'delete',
                'original'     => array_merge($donation->toArray(), ['member_name' => $donation->member?->full_name]),
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', 'تم إرسال طلب حذف التبرع للمراجعة، وسيُطبَّق بعد موافقة الأدمن.');
        }

        $memberName = $donation->member->full_name;
        $month      = $donation->donation_month->format('Y/m');
        $donation->delete();
        ActivityLogger::log('deleted', "حذف تبرع لـ {$memberName} ({$month})");

        return back()->with('success', 'تم حذف التبرع بنجاح.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\PaymentInfoAI;
use App\Models\PendingChange;
use App\Models\VerificationStatus;
use App\Models\MaritalStatus;
use App\Models\Association;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    private function formData(): array
    {
        return [
            'verificationStatuses' => VerificationStatus::active()->orderBy('name')->get(),
            'finalStatuses'        => FinalStatus::active()->orderBy('name')->get(),
            'maritalStatuses'      => MaritalStatus::active()->orderBy('id')->get(),
            'associations'         => Association::active()->orderBy('id')->get(),
            'representatives'      => User::orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $search             = $request->get('search');
        $dossierFrom        = trim($request->get('dossier_from', ''));
        $dossierTo          = trim($request->get('dossier_to', ''));
        $verificationIds    = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds     = array_filter((array) $request->get('final_status_id', []));
        $maritalStatuses    = array_filter((array) $request->get('marital_status', []));
        $genders            = array_filter((array) $request->get('gender', []));
        $delegates               = array_filter((array) $request->get('delegate', []));
        $specialCases            = $request->get('special_cases', '');
        $specialDescriptions     = array_filter((array) $request->get('special_cases_description', []));
        $addresses               = array_filter((array) $request->get('current_address', []));
        $associationIds          = array_filter((array) $request->get('association_id', []));
        $networks                = array_filter((array) $request->get('network', []));

        $query = Member::query()->with(['verificationStatus', 'representative', 'paymentInfo']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('dossier_number', 'like', "%{$search}%");
            });
        }
        if ($dossierFrom !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo   !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);

        if (!empty($verificationIds)) {
            $query->whereIn('verification_status_id', $verificationIds);
        }

        if (!empty($finalStatusIds)) {
            $query->whereIn('final_status_id', $finalStatusIds);
        }

        if (!empty($maritalStatuses)) {
            $query->whereIn('marital_status', $maritalStatuses);
        }

        if (!empty($genders)) {
            $query->whereIn('gender', $genders);
        }

        if (!empty($delegates)) {
            $query->whereIn('delegate', $delegates);
        }

        if ($specialCases !== '') {
            $query->where('special_cases', (bool) $specialCases);
        }

        if (!empty($specialDescriptions)) {
            $query->whereIn('special_cases_description', $specialDescriptions);
        }

        if (!empty($addresses)) {
            $query->whereIn('current_address', $addresses);
        }

        if (!empty($associationIds)) {
            $query->whereIn('association_id', $associationIds);
        }

        if (!empty($networks)) {
            $query->whereIn('network', $networks);
        }

        $totalAmount          = $query->sum('estimated_amount');
        $members              = $query->latest()->paginate(20)->withQueryString();

        // Collect duplicate IBANs for warning indicator
        $duplicateIbans = DB::table('payment_info')
            ->select('iban')
            ->whereNotNull('iban')
            ->where('iban', '!=', '')
            ->groupBy('iban')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('iban')
            ->flip()
            ->toArray();
        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList      = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList    = MaritalStatus::active()->orderBy('id')->get();
        $delegateList            = Member::whereNotNull('delegate')
                                         ->where('delegate', '!=', '')
                                         ->distinct()
                                         ->orderBy('delegate')
                                         ->pluck('delegate');

        $specialDescriptionList  = Member::whereNotNull('special_cases_description')
                                         ->where('special_cases_description', '!=', '')
                                         ->distinct()
                                         ->orderBy('special_cases_description')
                                         ->pluck('special_cases_description');

        $addressList             = Member::whereNotNull('current_address')
                                         ->where('current_address', '!=', '')
                                         ->distinct()
                                         ->orderBy('current_address')
                                         ->pluck('current_address');

        $associationList = Association::active()->orderBy('name')->get();

        return view('members.index', compact(
            'members', 'search', 'dossierFrom', 'dossierTo', 'totalAmount',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders', 'delegates', 'specialCases', 'specialDescriptions', 'addresses', 'associationIds', 'networks',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList', 'delegateList', 'specialDescriptionList', 'addressList', 'associationList',
            'duplicateIbans'
        ));
    }

    public function create()
    {
        ActivityLogger::log('viewed', 'فتح نموذج إضافة مستفيد جديد');
        return view('members.create', $this->formData());
    }

    public function show(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'paymentInfoAI', 'association', 'associations', 'verificationStatus', 'representative', 'images.uploader']);
        ActivityLogger::log('viewed', "عرض بيانات المستفيد: {$member->full_name}", $member);
        return view('members.show', compact('member'));
    }

    // ── Bulk Amount Editor ─────────────────────────────────────────────

    private function buildBulkAmountQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Member::query();

        $search              = $request->get('search');
        $dossierFrom         = trim($request->get('dossier_from', ''));
        $dossierTo           = trim($request->get('dossier_to', ''));
        $verificationIds     = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds      = array_filter((array) $request->get('final_status_id', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $networks            = array_filter((array) $request->get('network', []));
        $maritalStatuses     = array_filter((array) $request->get('marital_status', []));
        $hasAmount           = $request->get('has_amount', '');
        $genders             = array_filter((array) $request->get('gender', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $delegates           = array_filter((array) $request->get('delegate', []));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name',      'like', "%{$search}%")
                  ->orWhere('national_id',  'like', "%{$search}%")
                  ->orWhere('dossier_number','like', "%{$search}%");
            });
        }
        if ($dossierFrom !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo   !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);
        if (!empty($verificationIds))     $query->whereIn('verification_status_id', $verificationIds);
        if (!empty($finalStatusIds))      $query->whereIn('final_status_id', $finalStatusIds);
        if (!empty($addresses))           $query->whereIn('current_address', $addresses);
        if (!empty($networks))            $query->whereIn('network', $networks);
        if (!empty($maritalStatuses))     $query->whereIn('marital_status', $maritalStatuses);
        if ($hasAmount === '1')           $query->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0);
        if ($hasAmount === '0')           $query->where(fn($q) => $q->whereNull('estimated_amount')->orWhere('estimated_amount', 0));
        if (!empty($genders))             $query->whereIn('gender', $genders);
        if (!empty($associationIds))      $query->whereIn('association_id', $associationIds);
        if ($specialCases !== '')         $query->where('special_cases', (bool) $specialCases);
        if (!empty($specialDescriptions)) $query->whereIn('special_cases_description', $specialDescriptions);
        if (!empty($delegates))           $query->whereIn('delegate', $delegates);

        return $query;
    }

    public function bulkAmountShow(Request $request)
    {
        $base    = $this->buildBulkAmountQuery($request);
        $members = (clone $base)->with('verificationStatus')
                                ->orderBy('dossier_number')
                                ->paginate(60)->withQueryString();

        $totalCount  = (clone $base)->count();
        $withAmount  = (clone $base)->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0)->count();
        $totalAmount = (clone $base)->sum('estimated_amount');

        $verificationStatuses   = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList        = FinalStatus::active()->orderBy('name')->get();
        $addressList            = Member::whereNotNull('current_address')
                                        ->where('current_address', '!=', '')
                                        ->distinct()->orderBy('current_address')->pluck('current_address');
        $associationList        = Association::active()->orderBy('name')->get();
        $delegateList           = Member::whereNotNull('delegate')
                                        ->where('delegate', '!=', '')
                                        ->distinct()->orderBy('delegate')->pluck('delegate');
        $specialDescriptionList = Member::whereNotNull('special_cases_description')
                                        ->where('special_cases_description', '!=', '')
                                        ->distinct()->orderBy('special_cases_description')->pluck('special_cases_description');

        $dossierFrom = trim($request->get('dossier_from', ''));
        $dossierTo   = trim($request->get('dossier_to', ''));

        return view('members.bulk-amount', compact(
            'members', 'verificationStatuses', 'finalStatusList', 'addressList',
            'associationList', 'delegateList', 'specialDescriptionList',
            'totalCount', 'withAmount', 'totalAmount',
            'dossierFrom', 'dossierTo'
        ));
    }

    public function bulkAmountApply(Request $request)
    {
        $request->validate([
            'operation'  => 'required|in:add,subtract,set',
            'amount'     => 'required|numeric|min:0',
            'apply_to'   => 'required|in:selected,filtered',
            'member_ids' => 'array',
        ]);

        $amount    = (float) $request->amount;
        $operation = $request->operation;
        $applyTo   = $request->apply_to;

        $query = $applyTo === 'selected'
            ? Member::whereIn('id', $request->input('member_ids', []))
            : $this->buildBulkAmountQuery($request);

        $count = $query->count();

        if ($count === 0) {
            return back()->with('error', 'لم يتم تحديد أي أعضاء.');
        }

        $amtFmt = number_format($amount, 0);
        $operationLabels = ['add' => 'إضافة', 'subtract' => 'طرح', 'set' => 'تعيين'];
        $label = "{$operationLabels[$operation]} {$amtFmt} ل.س — {$count} عضو";

        if (!$this->isAdmin()) {
            // Resolve member IDs now so the apply() can replay exactly the same set later
            $memberIds = $query->pluck('id')->all();
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'bulk_amount',
                'payload'      => [
                    'operation'  => $operation,
                    'amount'     => $amount,
                    'member_ids' => $memberIds,
                    'count'      => $count,
                    'label'      => $label,
                ],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', "تم إرسال طلب تعديل المبالغ ({$label}) وهو بانتظار موافقة المسؤول.");
        }

        switch ($operation) {
            case 'add':
                $query->update(['estimated_amount' => DB::raw('COALESCE(estimated_amount, 0) + ' . $amount)]);
                break;
            case 'subtract':
                $query->update(['estimated_amount' => DB::raw('GREATEST(COALESCE(estimated_amount, 0) - ' . $amount . ', 0)')]);
                break;
            default: // set
                $query->update(['estimated_amount' => $amount]);
        }

        ActivityLogger::log('updated', "تعديل جماعي للمبلغ المقدر: {$label}");

        return back()->with('success', "تم {$label} بنجاح.");
    }

    // ───────────────────────────────────────────────────────────────────

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    private function buildMemberPayload(Request $request, ?Member $member = null): array
    {
        $scores = [
            'work_score'             => min(2,  (int)($request->work_score ?? 0)),
            'housing_score'          => min(4,  (int)($request->housing_score ?? 0)),
            'dependents_score'       => min(20, (int)($request->dependents_score ?? 0)),
            'dependent_status_score' => min(2,  (int)($request->dependent_status_score ?? 0)),
            'illness_score'          => min(5,  (int)($request->illness_score ?? 0)),
            'special_cases_score'    => min(10, (int)($request->special_cases_score ?? 0)),
        ];

        $payment = [
            'iban'    => $request->input('iban'),
            'barcode' => $request->input('barcode'),
        ];

        $paymentAI = [
            'iban'    => $request->input('iban_ai'),
            'barcode' => $request->input('barcode_ai'),
        ];

        if ($request->hasFile('iban_image')) {
            $id = $member?->id ?? 'pending';
            $payment['iban_image'] = $request->file('iban_image')->store("payment/iban_{$id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $id = $member?->id ?? 'pending';
            $payment['barcode_image'] = $request->file('barcode_image')->store("payment/barcode_{$id}", 'public');
        }

        return [
            'full_name'                 => $request->input('full_name'),
            'age'                       => $request->input('age'),
            'gender'                    => $request->input('gender'),
            'mother_name'               => $request->input('mother_name'),
            'national_id'               => $request->input('national_id'),
            'verification_status_id'    => $request->input('verification_status_id'),
            'final_status_id'           => $request->input('final_status_id') ?: null,
            'dossier_number'            => $request->input('dossier_number'),
            'current_address'           => $request->input('current_address'),
            'marital_status'            => $request->input('marital_status'),
            'disease_type'              => $request->input('disease_type'),
            'phone'                     => $request->input('phone'),
            'network'                   => $request->input('network'),
            'provider_status'           => $request->input('provider_status'),
            'job'                       => $request->input('job'),
            'housing_status'            => $request->input('housing_status'),
            'dependents_count'          => $request->input('dependents_count'),
            'illness_details'           => $request->input('illness_details'),
            'special_cases'             => $request->boolean('special_cases'),
            'special_cases_description' => $request->input('special_cases_description'),
            'sham_cash_account'         => $request->boolean('sham_cash_account'),
            'other_association'         => !empty($request->association_ids),
            'representative_id'         => $request->input('representative_id'),
            'delegate'                  => $request->input('delegate'),
            'association_id'            => $request->input('association_id'),
            'association_ids'           => $request->input('association_ids', []),
            'scores'                    => $scores,
            'payment'                   => $payment,
            'payment_ai'                => $paymentAI,
        ];
    }

    private function getMemberOriginal(Member $member): array
    {
        return array_merge(
            $member->only([
                'full_name', 'age', 'gender', 'mother_name', 'national_id',
                'verification_status_id', 'final_status_id', 'dossier_number', 'current_address',
                'marital_status', 'disease_type', 'phone', 'network', 'provider_status',
                'job', 'housing_status', 'dependents_count', 'illness_details',
                'special_cases', 'special_cases_description', 'sham_cash_account',
                'other_association', 'representative_id', 'delegate', 'association_id',
                'score', 'estimated_amount',
            ]),
            [
                'scores' => $member->scores?->only([
                    'work_score', 'housing_score', 'dependents_score',
                    'dependent_status_score', 'illness_score', 'special_cases_score', 'total_score',
                ]) ?? [],
                'payment' => $member->paymentInfo?->only([
                    'iban', 'barcode', 'iban_image', 'barcode_image',
                ]) ?? [],
                'payment_ai' => $member->paymentInfoAI?->only(['iban', 'barcode']) ?? [],
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'                  => 'required|string|max:255',
            'age'                        => 'nullable|integer|min:0|max:150',
            'gender'                     => 'nullable|string|max:10',
            'mother_name'                => 'nullable|string|max:255',
            'national_id'                => 'required|string|max:50|unique:members,national_id',
            'verification_status_id'     => 'required|exists:verification_statuses,id',
            'final_status_id'            => 'nullable|exists:final_statuses,id',
            'dossier_number'             => 'nullable|string|max:50|unique:members,dossier_number',
            'current_address'            => 'nullable|string',
            'marital_status'             => 'nullable|string|max:100',
            'disease_type'               => 'nullable|string|max:255',
            'phone'                      => 'nullable|string|max:50',
            'network'                    => 'nullable|in:MTN,SYRIATEL',
            'provider_status'            => 'nullable|string|max:100',
            'job'                        => 'nullable|string|max:150',
            'housing_status'             => 'nullable|string|max:150',
            'dependents_count'           => 'nullable|integer|min:0',
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
            'delegate'                   => 'nullable|string|max:255',
            'association_id'             => 'nullable|exists:associations,id',
            // scores
            'work_score'                 => 'nullable|integer|min:0|max:2',
            'housing_score'              => 'nullable|integer|min:0|max:4',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            // payment
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            // payment AI
            'iban_ai'                    => 'nullable|string|max:50',
            'barcode_ai'                 => 'nullable|string|max:100',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => $this->buildMemberPayload($request),
                'original'     => null,
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('created', 'طلب إضافة مستفيد جديد بانتظار موافقة المسؤول: ' . $request->input('full_name'));
            return redirect()->route('members.index')
                             ->with('pending', 'تم إرسال طلب إضافة المستفيد — بانتظار موافقة المسؤول.');
        }

        $workScore              = min(2,  (int)($request->work_score ?? 0));
        $housingScore           = min(4,  (int)($request->housing_score ?? 0));
        $dependentsScore        = min(20, (int)($request->dependents_score ?? 0));
        $dependentStatusScore   = min(2,  (int)($request->dependent_status_score ?? 0));
        $illnessScore           = min(5,  (int)($request->illness_score ?? 0));
        $specialScore           = min(10, (int)($request->special_cases_score ?? 0));
        $totalScore             = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;

        $member = Member::create([
            'full_name'                  => $data['full_name'],
            'age'                        => $data['age'] ?? null,
            'gender'                     => $data['gender'] ?? null,
            'mother_name'                => $data['mother_name'] ?? null,
            'national_id'                => $data['national_id'],
            'verification_status_id'     => $data['verification_status_id'],
            'final_status_id'            => $data['final_status_id'] ?? null,
            'dossier_number'             => $data['dossier_number'] ?? null,
            'current_address'            => $data['current_address'] ?? null,
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'representative_id'          => $data['representative_id'] ?? Auth::id(),
            'delegate'                   => $data['delegate'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status'             => $data['housing_status'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => $request->boolean('sham_cash_account'),
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
        ]);

        MemberScore::create([
            'member_id'              => $member->id,
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
        ]);

        $ibanImagePath    = null;
        $barcodeImagePath = null;

        if ($request->hasFile('iban_image')) {
            $ibanImagePath = $request->file('iban_image')->store("payment/iban_{$member->id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $barcodeImagePath = $request->file('barcode_image')->store("payment/barcode_{$member->id}", 'public');
        }

        PaymentInfo::create([
            'member_id'     => $member->id,
            'iban'          => $request->input('iban'),
            'barcode'       => $request->input('barcode'),
            'iban_image'    => $ibanImagePath,
            'barcode_image' => $barcodeImagePath,
        ]);

        PaymentInfoAI::create([
            'member_id' => $member->id,
            'iban'      => $request->input('iban_ai'),
            'barcode'   => $request->input('barcode_ai'),
        ]);

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('created', "إضافة مستفيد جديد: {$member->full_name}", $member);

        return redirect()->route('members.index')->with('success', 'تم إضافة المستفيد بنجاح.');
    }

    public function edit(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'paymentInfoAI', 'association', 'associations']);
        ActivityLogger::log('viewed', "فتح نموذج تعديل المستفيد: {$member->full_name}", $member);
        return view('members.edit', array_merge($this->formData(), compact('member')));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'full_name'                  => 'required|string|max:255',
            'age'                        => 'nullable|integer|min:0|max:150',
            'gender'                     => 'nullable|string|max:10',
            'mother_name'                => 'nullable|string|max:255',
            'national_id'                => 'required|string|max:50|unique:members,national_id,' . $member->id,
            'verification_status_id'     => 'required|exists:verification_statuses,id',
            'final_status_id'            => 'nullable|exists:final_statuses,id',
            'dossier_number'             => 'nullable|string|max:50|unique:members,dossier_number,' . $member->id,
            'current_address'            => 'nullable|string',
            'marital_status'             => 'nullable|string|max:100',
            'disease_type'               => 'nullable|string|max:255',
            'phone'                      => 'nullable|string|max:50',
            'network'                    => 'nullable|in:MTN,SYRIATEL',
            'provider_status'            => 'nullable|string|max:100',
            'job'                        => 'nullable|string|max:150',
            'housing_status'             => 'nullable|string|max:150',
            'dependents_count'           => 'nullable|integer|min:0',
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
            'delegate'                   => 'nullable|string|max:255',
            'association_id'             => 'nullable|exists:associations,id',
            'work_score'                 => 'nullable|integer|min:0|max:2',
            'housing_score'              => 'nullable|integer|min:0|max:4',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            // payment AI
            'iban_ai'                    => 'nullable|string|max:50',
            'barcode_ai'                 => 'nullable|string|max:100',
        ]);

        if (!$this->isAdmin()) {
            $member->load(['scores', 'paymentInfo', 'paymentInfoAI']);
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => $this->buildMemberPayload($request, $member),
                'original'     => $this->getMemberOriginal($member),
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('updated', "طلب تعديل بيانات المستفيد بانتظار موافقة المسؤول: {$member->full_name}", $member);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب التعديل — بانتظار موافقة المسؤول.');
        }

        $workScore            = min(2,  (int)($request->work_score ?? 0));
        $housingScore         = min(4,  (int)($request->housing_score ?? 0));
        $dependentsScore      = min(20, (int)($request->dependents_score ?? 0));
        $dependentStatusScore = min(2,  (int)($request->dependent_status_score ?? 0));
        $illnessScore         = min(5,  (int)($request->illness_score ?? 0));
        $specialScore         = min(10, (int)($request->special_cases_score ?? 0));
        $totalScore           = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;

        $member->update([
            'full_name'                  => $data['full_name'],
            'age'                        => $data['age'] ?? null,
            'gender'                     => $data['gender'] ?? null,
            'mother_name'                => $data['mother_name'] ?? null,
            'national_id'                => $data['national_id'],
            'verification_status_id'     => $data['verification_status_id'],
            'final_status_id'            => $data['final_status_id'] ?? null,
            'dossier_number'             => $data['dossier_number'] ?? null,
            'current_address'            => $data['current_address'] ?? null,
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'representative_id'          => $data['representative_id'] ?? $member->representative_id,
            'delegate'                   => $data['delegate'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status'             => $data['housing_status'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => $request->boolean('sham_cash_account'),
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
        ]);

        $scores = $member->scores ?? new MemberScore(['member_id' => $member->id]);
        $scores->fill([
            'member_id'              => $member->id,
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
        ])->save();

        $payment = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);

        if ($request->hasFile('iban_image')) {
            $payment->iban_image = $request->file('iban_image')->store("payment/iban_{$member->id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $payment->barcode_image = $request->file('barcode_image')->store("payment/barcode_{$member->id}", 'public');
        }

        $payment->fill([
            'member_id' => $member->id,
            'iban'      => $request->input('iban'),
            'barcode'   => $request->input('barcode'),
        ])->save();

        $paymentAI = $member->paymentInfoAI ?? new PaymentInfoAI(['member_id' => $member->id]);
        $paymentAI->fill([
            'member_id' => $member->id,
            'iban'      => $request->input('iban_ai'),
            'barcode'   => $request->input('barcode_ai'),
        ])->save();

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('updated', "تعديل بيانات المستفيد: {$member->full_name}", $member);

        return redirect()->route('members.index')->with('success', 'تم تحديث بيانات المستفيد بنجاح.');
    }

    public function destroy(Member $member)
    {
        if (!$this->isAdmin()) {
            $member->load(['scores', 'paymentInfo', 'paymentInfoAI']);
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'delete',
                'payload'      => null,
                'original'     => $this->getMemberOriginal($member),
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('deleted', "طلب حذف المستفيد بانتظار موافقة المسؤول: {$member->full_name}", $member);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب الحذف — بانتظار موافقة المسؤول.');
        }

        $name = $member->full_name;
        $member->delete();
        ActivityLogger::log('deleted', "حذف المستفيد: {$name}");
        return redirect()->route('members.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }

    public function updateFinalStatus(Request $request, Member $member)
    {
        $request->validate([
            'final_status_id' => 'nullable|exists:final_statuses,id',
        ]);

        $newStatusId = $request->input('final_status_id') ?: null;

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => ['final_status_id' => $newStatusId, 'full_name' => $member->full_name],
                'original'     => ['final_status_id' => $member->final_status_id, 'full_name' => $member->full_name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', "تم إرسال طلب تغيير الحالة النهائية لـ {$member->full_name} للمراجعة.");
        }

        $oldStatus = $member->finalStatus?->name ?? 'بدون';
        $member->update(['final_status_id' => $newStatusId]);
        $newStatus = $member->fresh()->finalStatus?->name ?? 'بدون';
        ActivityLogger::log('updated', "تغيير الحالة النهائية لـ {$member->full_name}: {$oldStatus} → {$newStatus}", $member);

        return back()->with('success', "تم تحديث الحالة النهائية لـ {$member->full_name}.");
    }
}

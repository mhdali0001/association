<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\VerificationStatus;
use App\Models\MaritalStatus;
use App\Models\Association;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    private function formData(): array
    {
        return [
            'verificationStatuses' => VerificationStatus::active()->orderBy('name')->get(),
            'maritalStatuses'      => MaritalStatus::active()->orderBy('id')->get(),
            'associations'         => Association::active()->orderBy('id')->get(),
            'representatives'      => User::orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $search            = $request->get('search');
        $verificationId    = $request->get('verification_status_id');
        $maritalStatus     = $request->get('marital_status');
        $gender            = $request->get('gender');

        $query = Member::query()->with(['verificationStatus', 'representative']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('dossier_number', 'like', "%{$search}%");
            });
        }

        if ($verificationId) {
            $query->where('verification_status_id', $verificationId);
        }

        if ($maritalStatus) {
            $query->where('marital_status', $maritalStatus);
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        $members            = $query->latest()->paginate(20)->withQueryString();
        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $maritalStatuses      = MaritalStatus::active()->orderBy('id')->get();

        return view('members.index', compact(
            'members', 'search',
            'verificationId', 'maritalStatus', 'gender',
            'verificationStatuses', 'maritalStatuses'
        ));
    }

    public function create()
    {
        ActivityLogger::log('viewed', 'فتح نموذج إضافة مستفيد جديد');
        return view('members.create', $this->formData());
    }

    public function show(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'association', 'associations', 'verificationStatus', 'representative']);
        ActivityLogger::log('viewed', "عرض بيانات المستفيد: {$member->full_name}", $member);
        return view('members.show', compact('member'));
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
            'housing_score'              => 'nullable|integer|min:0|max:2',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            // payment
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
        ]);

        $workScore              = min(2,  (int)($request->work_score ?? 0));
        $housingScore           = min(2,  (int)($request->housing_score ?? 0));
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

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('created', "إضافة مستفيد جديد: {$member->full_name}", $member);

        return redirect()->route('members.index')->with('success', 'تم إضافة المستفيد بنجاح.');
    }

    public function edit(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'association', 'associations']);
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
            'housing_score'              => 'nullable|integer|min:0|max:2',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
        ]);

        $workScore            = min(2,  (int)($request->work_score ?? 0));
        $housingScore         = min(2,  (int)($request->housing_score ?? 0));
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

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('updated', "تعديل بيانات المستفيد: {$member->full_name}", $member);

        return redirect()->route('members.index')->with('success', 'تم تحديث بيانات المستفيد بنجاح.');
    }

    public function destroy(Member $member)
    {
        $name = $member->full_name;
        $member->delete();
        ActivityLogger::log('deleted', "حذف المستفيد: {$name}");
        return redirect()->route('members.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }
}

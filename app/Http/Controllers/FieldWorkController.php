<?php

namespace App\Http\Controllers;

use App\Models\FieldVisitStatus;
use App\Models\HouseCondition;
use App\Models\HouseType;
use App\Models\Member;
use App\Models\Region;
use App\Models\Sector;
use App\Services\FieldVisitRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * العمل الميداني بدون إنترنت.
 * الصفحة نفسها لا تحوي بيانات مستفيدين، فيحفظها الـ Service Worker لتفتح بلا اتصال.
 * قائمة الزيارة تُنزَّل منها (حقول قليلة فقط) وتبقى على الجهاز يوماً واحداً،
 * والجولات المسجلة بلا اتصال تُرسل لاحقاً إلى storeVisit.
 */
class FieldWorkController extends Controller
{
    private const MAX_MEMBERS  = 300;
    private const PACKAGE_DAYS = 1;

    public function index()
    {
        return view('field-work.index', [
            'sectors' => Sector::active()->orderBy('name')->get(['id', 'name']),
            'regions' => Region::active()->orderBy('name')->get(['id', 'name', 'sector_id']),
        ]);
    }

    /** المستخدم الحالي ورمز حماية جديد — الصفحة المحفوظة قد تكون قديمة فلا تعتمد على رمزها */
    public function session(Request $request): JsonResponse
    {
        return response()->json([
            'user_id'   => Auth::id(),
            'user_name' => Auth::user()->name,
            'token'     => $request->session()->token(),
        ]);
    }

    public function package(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'sector_id'   => 'nullable|exists:sectors,id',
            'region_id'   => 'nullable|exists:regions,id',
            'search'      => 'nullable|string|max:100',
            'not_visited' => 'nullable|boolean',
        ]);

        $query = Member::query()
            ->when($filters['sector_id'] ?? null, fn ($q, $id) => $q->where('sector_id', $id))
            ->when($filters['region_id'] ?? null, fn ($q, $id) => $q->where('region_id', $id))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', "%{$s}%")
                ->orWhere('dossier_number', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")))
            ->when($request->boolean('not_visited'), fn ($q) => $q->doesntHave('fieldVisits'));

        $total   = (clone $query)->count();
        $members = $query->with(['region:id,name', 'latestFieldVisit.status:id,name'])
            ->withCount('fieldVisits')
            ->orderByRaw('CAST(dossier_number AS UNSIGNED)')
            ->limit(self::MAX_MEMBERS)
            ->get(['id', 'full_name', 'dossier_number', 'phone', 'phone2', 'current_address', 'region_id']);

        return response()->json([
            'user_id'      => Auth::id(),
            'user_name'    => Auth::user()->name,
            'generated_at' => now()->getTimestampMs(),
            'expires_at'   => now()->addDays(self::PACKAGE_DAYS)->getTimestampMs(),
            'total'        => $total,
            'limit'        => self::MAX_MEMBERS,
            // حقول الزيارة فقط — بلا رقم هوية ولا آيبان ولا تفاصيل الحالة
            'members'      => $members->map(fn (Member $m) => [
                'id'          => $m->id,
                'name'        => $m->full_name,
                'dossier'     => $m->dossier_number,
                'phone'       => $m->phone,
                'phone2'      => $m->phone2,
                'address'     => $m->current_address,
                'region'      => $m->region?->name,
                'visits'      => $m->field_visits_count,
                'last_visit'  => $m->latestFieldVisit?->visit_date?->format('Y-m-d'),
                'last_status' => $m->latestFieldVisit?->status?->name,
            ])->values(),
            'options'      => [
                'statuses'        => FieldVisitStatus::active()->orderBy('id')->get(['id', 'name']),
                'house_types'     => HouseType::active()->orderBy('id')->get(['id', 'name']),
                'house_conditions'=> HouseCondition::active()->orderBy('name')->get(['id', 'name']),
            ],
        ]);
    }

    /** جولة سُجّلت على الجهاز — آمن عند إعادة الإرسال بنفس client_id */
    public function storeVisit(Request $request, FieldVisitRecorder $recorder): JsonResponse
    {
        $data = $request->validate(FieldVisitRecorder::RULES + [
            'client_id' => 'required|uuid',
            'member_id' => 'required|integer|exists:members,id',
        ], [
            'member_id.exists' => 'هذا المستفيد لم يعد موجوداً في النظام.',
        ]);

        $member   = Member::findOrFail($data['member_id']);
        $clientId = $data['client_id'];
        unset($data['client_id'], $data['member_id']);

        return response()->json([
            'status' => $recorder->record($member, $recorder->normalize($data, $request), $clientId),
        ]);
    }
}

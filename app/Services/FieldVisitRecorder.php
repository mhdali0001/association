<?php

namespace App\Services;

use App\Models\FieldVisit;
use App\Models\Member;
use App\Models\PendingChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * تسجيل جولة ميدانية جديدة — مشترك بين نموذج صفحة العضو وصفحة «العمل الميداني» (بدون إنترنت):
 * المسؤول تُضاف جولته مباشرة، وغيره تُرسل طلباً بانتظار الموافقة.
 */
class FieldVisitRecorder
{
    public const RULES = [
        'field_visit_status_id' => 'nullable|exists:field_visit_statuses,id',
        'house_type_id'         => 'nullable|exists:house_types,id',
        'visit_date'            => 'nullable|date',
        'visitor'               => 'nullable|string|max:255',
        'estimated_amount'      => 'nullable|numeric|min:0',
        'amount_operation'      => 'nullable|in:add,subtract',
        'amount_reason'         => 'nullable|string',
        'notes'                 => 'nullable|string',
        'house_condition_id'    => 'nullable|exists:house_conditions,id',
        'has_video'             => 'nullable|boolean',
        'has_special_case'      => 'nullable|boolean',
    ];

    /** يحوّل حقول النموذج إلى أعمدة الجولة (الإنقاص مبلغ سالب، والتاريخ الافتراضي اليوم) */
    public function normalize(array $data, Request $request): array
    {
        $data['has_video']        = $request->boolean('has_video');
        $data['has_special_case'] = $request->boolean('has_special_case');
        $data['visit_date']       = $data['visit_date'] ?? now()->toDateString();

        if (isset($data['estimated_amount']) && ($data['amount_operation'] ?? 'add') === 'subtract') {
            $data['estimated_amount'] = -abs($data['estimated_amount']);
        }
        unset($data['amount_operation']);

        return $data;
    }

    /**
     * @param  string|null  $clientId  معرّف الجولة من الجهاز — يمنع تكرارها عند إعادة الإرسال
     * @return string 'created' | 'pending' | 'duplicate'
     */
    public function record(Member $member, array $data, ?string $clientId = null): string
    {
        if ($clientId === null) {
            return $this->save($member, $data, null);
        }

        // قفل على المعرّف: إعادة إرسال وصلت قبل انتهاء الطلب الأول لا تُنشئ جولة ثانية
        return Cache::lock("field-visit-client:{$clientId}", 30)->block(10, function () use ($member, $data, $clientId) {
            $alreadyRecorded = FieldVisit::where('client_id', $clientId)->exists()
                || PendingChange::where('model_type', 'field_visit')->where('payload->client_id', $clientId)->exists();

            return $alreadyRecorded ? 'duplicate' : $this->save($member, $data, $clientId);
        });
    }

    private function save(Member $member, array $data, ?string $clientId): string
    {
        if (Auth::user()?->role !== 'admin') {
            PendingChange::create([
                'model_type'   => 'field_visit',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => array_merge($data, array_filter([
                    'member_id'   => $member->id,
                    'member_name' => $member->full_name,
                    'client_id'   => $clientId,
                ])),
                'original'     => [],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return 'pending';
        }

        $member->fieldVisits()->create($data + ['created_by' => Auth::id(), 'client_id' => $clientId]);
        ActivityLogger::log('created', "إضافة جولة ميدانية للمستفيد: {$member->full_name}", $member);

        return 'created';
    }
}

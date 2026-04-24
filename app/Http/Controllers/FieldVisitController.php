<?php
namespace App\Http\Controllers;

use App\Models\FieldVisit;
use App\Models\Member;
use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldVisitController extends Controller
{
    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function store(Request $request, Member $member)
    {
        $data = $request->validate([
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
        ]);
        $data['has_video']        = $request->boolean('has_video');
        $data['has_special_case'] = $request->boolean('has_special_case');

        if (isset($data['estimated_amount']) && ($data['amount_operation'] ?? 'add') === 'subtract') {
            $data['estimated_amount'] = -abs($data['estimated_amount']);
        }
        unset($data['amount_operation']);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'field_visit',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => array_merge($data, [
                    'member_id'   => $member->id,
                    'member_name' => $member->full_name,
                ]),
                'original'     => [],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب إضافة الجولة الميدانية — بانتظار موافقة المسؤول.');
        }

        $member->fieldVisits()->create($data);
        $this->recomputeFinalAmount($member);
        ActivityLogger::log('created', "إضافة جولة ميدانية للمستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تمت إضافة الجولة الميدانية بنجاح.');
    }

    public function update(Request $request, Member $member, FieldVisit $fieldVisit)
    {
        abort_if($fieldVisit->member_id !== $member->id, 404);

        $data = $request->validate([
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
        ]);
        $data['has_video']        = $request->boolean('has_video');
        $data['has_special_case'] = $request->boolean('has_special_case');

        if (isset($data['estimated_amount']) && ($data['amount_operation'] ?? 'add') === 'subtract') {
            $data['estimated_amount'] = -abs($data['estimated_amount']);
        }
        unset($data['amount_operation']);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'field_visit',
                'model_id'     => $fieldVisit->id,
                'action'       => 'update',
                'payload'      => array_merge($data, [
                    'member_id'   => $member->id,
                    'member_name' => $member->full_name,
                ]),
                'original'     => [
                    'member_name'           => $member->full_name,
                    'field_visit_status_id' => $fieldVisit->field_visit_status_id,
                    'house_type_id'         => $fieldVisit->house_type_id,
                    'visit_date'            => $fieldVisit->visit_date?->format('Y-m-d'),
                    'visitor'               => $fieldVisit->visitor,
                    'estimated_amount'      => $fieldVisit->estimated_amount,
                    'amount_reason'         => $fieldVisit->amount_reason,
                    'notes'                 => $fieldVisit->notes,
                    'house_condition_id'    => $fieldVisit->house_condition_id,
                    'has_video'             => $fieldVisit->has_video,
                    'has_special_case'      => $fieldVisit->has_special_case,
                ],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب تعديل الجولة الميدانية — بانتظار موافقة المسؤول.');
        }

        $fieldVisit->update($data);
        $this->recomputeFinalAmount($member);
        ActivityLogger::log('updated', "تعديل جولة ميدانية للمستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تم تحديث الجولة الميدانية بنجاح.');
    }

    public function adjustAmount(Request $request, Member $member, FieldVisit $fieldVisit)
    {
        abort_if($fieldVisit->member_id !== $member->id, 404);

        $data = $request->validate([
            'operation'     => 'required|in:add,subtract',
            'amount'        => 'required|numeric|min:0.01',
            'amount_reason' => 'nullable|string|max:255',
        ]);

        $current    = $fieldVisit->estimated_amount ?? 0;
        $adjustment = (float) $data['amount'];
        $newAmount  = $data['operation'] === 'add'
            ? $current + $adjustment
            : max(0, $current - $adjustment);

        $sign   = $data['operation'] === 'add' ? '+' : '-';
        $label  = $data['operation'] === 'add' ? 'إضافة' : 'إنقاص';
        $reason = $data['amount_reason'] ?: "{$label} {$adjustment} ل.س";

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'field_visit',
                'model_id'     => $fieldVisit->id,
                'action'       => 'update',
                'payload'      => ['estimated_amount' => $newAmount, 'amount_reason' => $reason, 'member_id' => $member->id, 'member_name' => $member->full_name],
                'original'     => ['estimated_amount' => $current, 'amount_reason' => $fieldVisit->amount_reason, 'member_name' => $member->full_name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('pending', "تم إرسال طلب تعديل المبلغ ({$sign}{$adjustment}) — بانتظار موافقة المسؤول.");
        }

        $fieldVisit->update(['estimated_amount' => $newAmount, 'amount_reason' => $reason]);
        $this->recomputeFinalAmount($member);
        ActivityLogger::log('updated', "تعديل مبلغ جولة ({$sign}{$adjustment} ل.س) للمستفيد: {$member->full_name}", $member);

        return back()->with('success', "تم تعديل المبلغ: {$sign}" . number_format($adjustment) . " ل.س → المبلغ الجديد: " . number_format($newAmount) . " ل.س");
    }

    public function destroy(Member $member, FieldVisit $fieldVisit)
    {
        abort_if($fieldVisit->member_id !== $member->id, 404);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'field_visit',
                'model_id'     => $fieldVisit->id,
                'action'       => 'delete',
                'payload'      => [
                    'member_id'             => $member->id,
                    'member_name'           => $member->full_name,
                    'field_visit_status_id' => $fieldVisit->field_visit_status_id,
                    'house_type_id'         => $fieldVisit->house_type_id,
                    'visit_date'            => $fieldVisit->visit_date?->format('Y-m-d'),
                    'visitor'               => $fieldVisit->visitor,
                    'estimated_amount'      => $fieldVisit->estimated_amount,
                    'amount_reason'         => $fieldVisit->amount_reason,
                    'notes'                 => $fieldVisit->notes,
                    'house_condition_id'    => $fieldVisit->house_condition_id,
                    'has_video'             => $fieldVisit->has_video,
                    'has_special_case'      => $fieldVisit->has_special_case,
                ],
                'original'     => [],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب حذف الجولة الميدانية — بانتظار موافقة المسؤول.');
        }

        $fieldVisit->delete();
        $this->recomputeFinalAmount($member);
        ActivityLogger::log('deleted', "حذف جولة ميدانية للمستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تم حذف الجولة الميدانية.');
    }

    private function recomputeFinalAmount(Member $member): void
    {
        $visitAmount = $member->fieldVisits()->latest()->value('estimated_amount') ?? 0;
        $member->update(['final_amount' => ($member->estimated_amount ?? 0) + $visitAmount]);
    }
}

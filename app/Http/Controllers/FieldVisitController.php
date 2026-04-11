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
            'visit_date'            => 'nullable|date',
            'visitor'               => 'nullable|string|max:255',
            'estimated_amount'      => 'nullable|numeric|min:0',
            'amount_reason'         => 'nullable|string',
            'notes'                 => 'nullable|string',
        ]);

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
            'visit_date'            => 'nullable|date',
            'visitor'               => 'nullable|string|max:255',
            'estimated_amount'      => 'nullable|numeric|min:0',
            'amount_reason'         => 'nullable|string',
            'notes'                 => 'nullable|string',
        ]);

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
                    'visit_date'            => $fieldVisit->visit_date?->format('Y-m-d'),
                    'visitor'               => $fieldVisit->visitor,
                    'estimated_amount'      => $fieldVisit->estimated_amount,
                    'amount_reason'         => $fieldVisit->amount_reason,
                    'notes'                 => $fieldVisit->notes,
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
                    'visit_date'            => $fieldVisit->visit_date?->format('Y-m-d'),
                    'visitor'               => $fieldVisit->visitor,
                    'estimated_amount'      => $fieldVisit->estimated_amount,
                    'amount_reason'         => $fieldVisit->amount_reason,
                    'notes'                 => $fieldVisit->notes,
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

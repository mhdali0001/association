<?php
namespace App\Http\Controllers;

use App\Models\FieldVisit;
use App\Models\Member;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class FieldVisitController extends Controller
{
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

        $fieldVisit->update($data);
        $this->recomputeFinalAmount($member);
        ActivityLogger::log('updated', "تعديل جولة ميدانية للمستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تم تحديث الجولة الميدانية بنجاح.');
    }

    public function destroy(Member $member, FieldVisit $fieldVisit)
    {
        abort_if($fieldVisit->member_id !== $member->id, 404);
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

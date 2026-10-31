<?php

namespace App\Http\Controllers;

use App\Models\FieldVisitStatus;
use App\Models\HouseCondition;
use App\Models\HouseType;
use App\Models\Member;
use App\Services\FieldVisitRecorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * تسجيل الجولات الميدانية بدون إنترنت: رقم الإضبارة + حقول الجولة.
 * الصفحة لا تحوي بيانات مستفيدين، فيحفظها الـ Service Worker لتفتح بلا اتصال،
 * والجولات المحفوظة على الجهاز تُرسل لاحقاً إلى storeVisit.
 */
class FieldWorkController extends Controller
{
    public function index()
    {
        return view('field-work.index', [
            'statuses'        => FieldVisitStatus::active()->orderBy('id')->get(['id', 'name']),
            'houseTypes'      => HouseType::active()->orderBy('id')->get(['id', 'name']),
            'houseConditions' => HouseCondition::active()->orderBy('name')->get(['id', 'name']),
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

    /** اسم المستفيد لرقم الإضبارة — يظهر تحت الحقل للتأكد حين يتوفر الإنترنت */
    public function member(Request $request): JsonResponse
    {
        $member = Member::where('dossier_number', trim((string) $request->query('dossier')))->first(['full_name']);

        return $member
            ? response()->json(['name' => $member->full_name])
            : response()->json(['message' => 'لا يوجد مستفيد برقم الإضبارة هذا.'], 404);
    }

    /** جولة سُجّلت على الجهاز — آمن عند إعادة الإرسال بنفس client_id */
    public function storeVisit(Request $request, FieldVisitRecorder $recorder): JsonResponse
    {
        $data = $request->validate(FieldVisitRecorder::RULES + [
            'client_id'      => 'required|uuid',
            'dossier_number' => 'required_without:member_id|nullable|string|exists:members,dossier_number',
            // جولات حُفظت بالنسخة السابقة من الصفحة (كانت ترسل رقم العضو)
            'member_id'      => 'nullable|integer|exists:members,id',
        ], [
            'dossier_number.required_without' => 'رقم الإضبارة مطلوب.',
            'dossier_number.exists'           => 'لا يوجد مستفيد برقم الإضبارة هذا.',
            'member_id.exists'                => 'هذا المستفيد لم يعد موجوداً في النظام.',
        ]);

        $member = isset($data['dossier_number'])
            ? Member::where('dossier_number', $data['dossier_number'])->firstOrFail()
            : Member::findOrFail($data['member_id']);
        $clientId = $data['client_id'];
        unset($data['client_id'], $data['dossier_number'], $data['member_id']);

        return response()->json([
            'status' => $recorder->record($member, $recorder->normalize($data, $request), $clientId),
            'member' => $member->full_name,
        ]);
    }
}

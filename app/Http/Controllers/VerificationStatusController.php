<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use App\Models\VerificationStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationStatusController extends Controller
{
    public function index()
    {
        $statuses = VerificationStatus::orderBy('id')->get();
        return view('verification-statuses.index', compact('statuses'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:verification_statuses,name',
            'color' => 'required|string|max:20',
        ], [
            'name.required'  => 'الاسم مطلوب.',
            'name.unique'    => 'حالة التحقق هذه موجودة مسبقاً.',
            'color.required' => 'اللون مطلوب.',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'verification_status',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'color' => $data['color'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('verification-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $status = VerificationStatus::create([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => true,
        ]);
        ActivityLogger::log('created', "إضافة حالة تحقق: {$status->name}", $status);

        return redirect()->route('verification-statuses.index')->with('success', 'تمت إضافة حالة التحقق بنجاح.');
    }

    public function update(Request $request, VerificationStatus $verificationStatus)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:verification_statuses,name,' . $verificationStatus->id,
            'color' => 'required|string|max:20',
        ], [
            'name.required'  => 'الاسم مطلوب.',
            'name.unique'    => 'حالة التحقق هذه موجودة مسبقاً.',
            'color.required' => 'اللون مطلوب.',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'verification_status',
                'model_id'     => $verificationStatus->id,
                'action'       => 'update',
                'payload'      => ['name' => $data['name'], 'color' => $data['color'], 'is_active' => $request->boolean('is_active')],
                'original'     => ['name' => $verificationStatus->name, 'color' => $verificationStatus->color, 'is_active' => $verificationStatus->is_active],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('verification-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $verificationStatus->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل حالة تحقق: {$verificationStatus->name}", $verificationStatus);

        return redirect()->route('verification-statuses.index')->with('success', 'تم تحديث حالة التحقق بنجاح.');
    }

    public function destroy(VerificationStatus $verificationStatus)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'verification_status',
                'model_id'     => $verificationStatus->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => ['name' => $verificationStatus->name, 'color' => $verificationStatus->color],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('verification-statuses.index')->with('success', 'تم إرسال طلب الحذف وهو بانتظار موافقة المسؤول.');
        }

        $name = $verificationStatus->name;
        $verificationStatus->delete();
        ActivityLogger::log('deleted', "حذف حالة تحقق: {$name}");

        return redirect()->route('verification-statuses.index')->with('success', 'تم حذف حالة التحقق بنجاح.');
    }
}

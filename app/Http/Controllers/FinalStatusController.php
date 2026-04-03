<?php

namespace App\Http\Controllers;

use App\Models\FinalStatus;
use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinalStatusController extends Controller
{
    public function index()
    {
        $statuses = FinalStatus::orderBy('id')->get();
        return view('final-statuses.index', compact('statuses'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:final_statuses,name',
            'color' => 'required|string|max:20',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الحالة موجودة مسبقاً.',
            'name.max'      => 'الاسم طويل جداً (أقصى 100 حرف).',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'final_status',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'color' => $data['color'], 'is_active' => 1],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('final-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $status = FinalStatus::create(['name' => $data['name'], 'color' => $data['color'], 'is_active' => 1]);
        ActivityLogger::log('created', "إضافة حالة نهائية: {$status->name}", $status);

        return redirect()->route('final-statuses.index')->with('success', 'تمت إضافة الحالة النهائية بنجاح.');
    }

    public function update(Request $request, FinalStatus $finalStatus)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:final_statuses,name,' . $finalStatus->id,
            'color' => 'required|string|max:20',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الحالة موجودة مسبقاً.',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'final_status',
                'model_id'     => $finalStatus->id,
                'action'       => 'update',
                'payload'      => ['name' => $data['name'], 'color' => $data['color'], 'is_active' => $request->boolean('is_active') ? 1 : 0],
                'original'     => ['name' => $finalStatus->name, 'color' => $finalStatus->color, 'is_active' => $finalStatus->is_active],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('final-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $finalStatus->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);
        ActivityLogger::log('updated', "تعديل حالة نهائية: {$finalStatus->name}", $finalStatus);

        return redirect()->route('final-statuses.index')->with('success', 'تم تحديث الحالة النهائية بنجاح.');
    }

    public function destroy(FinalStatus $finalStatus)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'final_status',
                'model_id'     => $finalStatus->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => ['name' => $finalStatus->name, 'color' => $finalStatus->color],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('final-statuses.index')->with('success', 'تم إرسال طلب الحذف وهو بانتظار موافقة المسؤول.');
        }

        $name = $finalStatus->name;
        $finalStatus->delete();
        ActivityLogger::log('deleted', "حذف حالة نهائية: {$name}");

        return redirect()->route('final-statuses.index')->with('success', 'تم حذف الحالة النهائية بنجاح.');
    }
}

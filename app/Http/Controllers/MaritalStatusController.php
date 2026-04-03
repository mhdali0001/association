<?php

namespace App\Http\Controllers;

use App\Models\MaritalStatus;
use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaritalStatusController extends Controller
{
    public function index()
    {
        $statuses = MaritalStatus::orderBy('id')->get();
        return view('marital-statuses.index', compact('statuses'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:marital_statuses,name',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الحالة الاجتماعية موجودة مسبقاً.',
            'name.max'      => 'الاسم طويل جداً (أقصى 50 حرف).',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'marital_status',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => 1],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('marital-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $status = MaritalStatus::create(['name' => $data['name'], 'is_active' => 1]);
        ActivityLogger::log('created', "إضافة حالة اجتماعية: {$status->name}", $status);

        return redirect()->route('marital-statuses.index')->with('success', 'تمت إضافة الحالة الاجتماعية بنجاح.');
    }

    public function update(Request $request, MaritalStatus $maritalStatus)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:marital_statuses,name,' . $maritalStatus->id,
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الحالة الاجتماعية موجودة مسبقاً.',
            'name.max'      => 'الاسم طويل جداً (أقصى 50 حرف).',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'marital_status',
                'model_id'     => $maritalStatus->id,
                'action'       => 'update',
                'payload'      => ['name' => $data['name'], 'is_active' => $request->boolean('is_active') ? 1 : 0],
                'original'     => ['name' => $maritalStatus->name, 'is_active' => $maritalStatus->is_active],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('marital-statuses.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $maritalStatus->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);
        ActivityLogger::log('updated', "تعديل حالة اجتماعية: {$maritalStatus->name}", $maritalStatus);

        return redirect()->route('marital-statuses.index')->with('success', 'تم تحديث الحالة الاجتماعية بنجاح.');
    }

    public function destroy(MaritalStatus $maritalStatus)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'marital_status',
                'model_id'     => $maritalStatus->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => ['name' => $maritalStatus->name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('marital-statuses.index')->with('success', 'تم إرسال طلب الحذف وهو بانتظار موافقة المسؤول.');
        }

        $name = $maritalStatus->name;
        $maritalStatus->delete();
        ActivityLogger::log('deleted', "حذف حالة اجتماعية: {$name}");

        return redirect()->route('marital-statuses.index')->with('success', 'تم حذف الحالة الاجتماعية بنجاح.');
    }
}

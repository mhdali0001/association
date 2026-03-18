<?php

namespace App\Http\Controllers;

use App\Models\MaritalStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    public function index()
    {
        $statuses = MaritalStatus::orderBy('id')->get();
        return view('marital-statuses.index', compact('statuses'));
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

        $maritalStatus->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active') ? 1 : 0,
        ]);
        ActivityLogger::log('updated', "تعديل حالة اجتماعية: {$maritalStatus->name}", $maritalStatus);

        return redirect()->route('marital-statuses.index')->with('success', 'تم تحديث الحالة الاجتماعية بنجاح.');
    }

    public function destroy(MaritalStatus $maritalStatus)
    {
        $name = $maritalStatus->name;
        $maritalStatus->delete();
        ActivityLogger::log('deleted', "حذف حالة اجتماعية: {$name}");

        return redirect()->route('marital-statuses.index')->with('success', 'تم حذف الحالة الاجتماعية بنجاح.');
    }
}

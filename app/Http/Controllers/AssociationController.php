<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssociationController extends Controller
{
    public function index()
    {
        $associations = Association::withCount('members')->orderBy('id')->get();
        return view('associations.index', compact('associations'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:associations,name',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الجمعية موجودة مسبقاً.',
            'name.max'      => 'الاسم طويل جداً (أقصى 150 حرف).',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'association',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('associations.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $association = Association::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة جمعية: {$association->name}", $association);

        return redirect()->route('associations.index')->with('success', 'تمت إضافة الجمعية بنجاح.');
    }

    public function update(Request $request, Association $association)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:associations,name,' . $association->id,
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'هذه الجمعية موجودة مسبقاً.',
            'name.max'      => 'الاسم طويل جداً (أقصى 150 حرف).',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'association',
                'model_id'     => $association->id,
                'action'       => 'update',
                'payload'      => ['name' => $data['name'], 'is_active' => $request->boolean('is_active')],
                'original'     => ['name' => $association->name, 'is_active' => $association->is_active],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('associations.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $association->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل جمعية: {$association->name}", $association);

        return redirect()->route('associations.index')->with('success', 'تم تحديث الجمعية بنجاح.');
    }

    public function destroy(Association $association)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'association',
                'model_id'     => $association->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => ['name' => $association->name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('associations.index')->with('success', 'تم إرسال طلب الحذف وهو بانتظار موافقة المسؤول.');
        }

        $name = $association->name;
        $association->delete();
        ActivityLogger::log('deleted', "حذف جمعية: {$name}");

        return redirect()->route('associations.index')->with('success', 'تم حذف الجمعية بنجاح.');
    }
}

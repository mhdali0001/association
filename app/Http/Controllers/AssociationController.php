<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AssociationController extends Controller
{
    public function index()
    {
        $associations = Association::withCount('members')->orderBy('id')->get();
        return view('associations.index', compact('associations'));
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

        $association->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل جمعية: {$association->name}", $association);

        return redirect()->route('associations.index')->with('success', 'تم تحديث الجمعية بنجاح.');
    }

    public function destroy(Association $association)
    {
        $name = $association->name;
        $association->delete();
        ActivityLogger::log('deleted', "حذف جمعية: {$name}");

        return redirect()->route('associations.index')->with('success', 'تم حذف الجمعية بنجاح.');
    }
}

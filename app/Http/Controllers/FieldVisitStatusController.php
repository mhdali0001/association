<?php
namespace App\Http\Controllers;

use App\Models\FieldVisitStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldVisitStatusController extends Controller
{
    public function index()
    {
        $statuses = FieldVisitStatus::withCount('fieldVisits')->orderBy('id')->get();
        return view('field-visit-statuses.index', compact('statuses'));
    }

    private function isAdmin(): bool { return Auth::user()?->role === 'admin'; }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:field_visit_statuses,name',
            'color' => 'required|string|max:20',
        ]);

        FieldVisitStatus::create(['name' => $data['name'], 'color' => $data['color'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة حالة جولة ميدانية: {$data['name']}");

        return redirect()->route('field-visit-statuses.index')->with('success', 'تمت إضافة الحالة بنجاح.');
    }

    public function update(Request $request, FieldVisitStatus $fieldVisitStatus)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:field_visit_statuses,name,' . $fieldVisitStatus->id,
            'color' => 'required|string|max:20',
        ]);

        $fieldVisitStatus->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل حالة جولة ميدانية: {$fieldVisitStatus->name}");

        return redirect()->route('field-visit-statuses.index')->with('success', 'تم تحديث الحالة بنجاح.');
    }

    public function destroy(FieldVisitStatus $fieldVisitStatus)
    {
        $name = $fieldVisitStatus->name;
        $fieldVisitStatus->delete();
        ActivityLogger::log('deleted', "حذف حالة جولة ميدانية: {$name}");

        return redirect()->route('field-visit-statuses.index')->with('success', 'تم حذف الحالة بنجاح.');
    }
}

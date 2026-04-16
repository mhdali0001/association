<?php
namespace App\Http\Controllers;

use App\Models\HousingStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousingStatusController extends Controller
{
    public function index()
    {
        $statuses = HousingStatus::withCount('members')->orderBy('id')->get();
        return view('housing-statuses.index', compact('statuses'));
    }

    private function isAdmin(): bool { return Auth::user()?->role === 'admin'; }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:housing_statuses,name',
            'color' => 'required|string|max:20',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'وضع السكن هذا موجود مسبقاً.',
            'color.required'=> 'اللون مطلوب.',
        ]);

        $status = HousingStatus::create(['name' => $data['name'], 'color' => $data['color'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة وضع سكن: {$status->name}", $status);

        return redirect()->route('housing-statuses.index')->with('success', 'تمت إضافة وضع السكن بنجاح.');
    }

    public function update(Request $request, HousingStatus $housingStatus)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:housing_statuses,name,' . $housingStatus->id,
            'color' => 'required|string|max:20',
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.unique'   => 'وضع السكن هذا موجود مسبقاً.',
            'color.required'=> 'اللون مطلوب.',
        ]);

        $housingStatus->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل وضع سكن: {$housingStatus->name}", $housingStatus);

        return redirect()->route('housing-statuses.index')->with('success', 'تم تحديث وضع السكن بنجاح.');
    }

    public function destroy(HousingStatus $housingStatus)
    {
        $name = $housingStatus->name;
        $housingStatus->delete();
        ActivityLogger::log('deleted', "حذف وضع سكن: {$name}");

        return redirect()->route('housing-statuses.index')->with('success', 'تم حذف وضع السكن بنجاح.');
    }
}

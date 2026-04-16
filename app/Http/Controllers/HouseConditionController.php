<?php
namespace App\Http\Controllers;

use App\Models\HouseCondition;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class HouseConditionController extends Controller
{
    public function index()
    {
        $conditions = HouseCondition::withCount('fieldVisits')->orderBy('id')->get();
        return view('house-conditions.index', compact('conditions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:house_conditions,name',
            'color' => 'required|string|max:20',
        ]);

        HouseCondition::create(['name' => $data['name'], 'color' => $data['color'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة حالة بيت: {$data['name']}");

        return redirect()->route('house-conditions.index')->with('success', 'تمت إضافة حالة البيت بنجاح.');
    }

    public function update(Request $request, HouseCondition $houseCondition)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:house_conditions,name,' . $houseCondition->id,
            'color' => 'required|string|max:20',
        ]);

        $houseCondition->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل حالة بيت: {$houseCondition->name}");

        return redirect()->route('house-conditions.index')->with('success', 'تم تحديث حالة البيت بنجاح.');
    }

    public function destroy(HouseCondition $houseCondition)
    {
        $name = $houseCondition->name;
        $houseCondition->delete();
        ActivityLogger::log('deleted', "حذف حالة بيت: {$name}");

        return redirect()->route('house-conditions.index')->with('success', 'تم حذف حالة البيت بنجاح.');
    }
}

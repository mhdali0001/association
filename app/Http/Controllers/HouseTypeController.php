<?php
namespace App\Http\Controllers;

use App\Models\HouseType;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseTypeController extends Controller
{
    public function index()
    {
        $types = HouseType::withCount('fieldVisits')->orderBy('id')->get();
        return view('house-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:house_types,name',
            'color' => 'required|string|max:20',
        ]);

        HouseType::create(['name' => $data['name'], 'color' => $data['color'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة نوع بيت: {$data['name']}");

        return redirect()->route('house-types.index')->with('success', 'تمت إضافة نوع البيت بنجاح.');
    }

    public function update(Request $request, HouseType $houseType)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100|unique:house_types,name,' . $houseType->id,
            'color' => 'required|string|max:20',
        ]);

        $houseType->update([
            'name'      => $data['name'],
            'color'     => $data['color'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل نوع بيت: {$houseType->name}");

        return redirect()->route('house-types.index')->with('success', 'تم تحديث نوع البيت بنجاح.');
    }

    public function destroy(HouseType $houseType)
    {
        $name = $houseType->name;
        $houseType->delete();
        ActivityLogger::log('deleted', "حذف نوع بيت: {$name}");

        return redirect()->route('house-types.index')->with('success', 'تم حذف نوع البيت بنجاح.');
    }
}

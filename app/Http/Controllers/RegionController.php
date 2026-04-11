<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount('members')->orderBy('name')->get();
        return view('regions.index', compact('regions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name',
        ]);

        Region::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة منطقة: {$data['name']}");

        return redirect()->route('regions.index')->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    public function update(Request $request, Region $region)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name,' . $region->id,
        ]);

        $region->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل منطقة: {$region->name}");

        return redirect()->route('regions.index')->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    public function destroy(Region $region)
    {
        $name = $region->name;
        $region->delete();
        ActivityLogger::log('deleted', "حذف منطقة: {$name}");

        return redirect()->route('regions.index')->with('success', 'تم حذف المنطقة.');
    }
}

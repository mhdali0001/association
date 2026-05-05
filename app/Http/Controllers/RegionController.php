<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use App\Models\Region;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionController extends Controller
{
    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function index()
    {
        $regions = Region::withCount('members')->orderBy('name')->get();
        return view('regions.index', compact('regions'));
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'region',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return response()->json([
                'pending' => true,
                'message' => 'تم إرسال طلب إضافة المنطقة "' . $data['name'] . '" — بانتظار موافقة المسؤول.',
            ]);
        }

        $region = Region::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة منطقة: {$data['name']}");

        return response()->json(['id' => $region->id, 'name' => $region->name]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'region',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('regions.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        Region::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة منطقة: {$data['name']}");

        return redirect()->route('regions.index')->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    public function update(Request $request, Region $region)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:regions,name,' . $region->id,
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'region',
                'model_id'     => $region->id,
                'action'       => 'update',
                'payload'      => ['name' => $data['name'], 'is_active' => $request->boolean('is_active')],
                'original'     => ['name' => $region->name, 'is_active' => $region->is_active],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('regions.index')->with('success', 'تم إرسال الطلب وهو بانتظار موافقة المسؤول.');
        }

        $region->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل منطقة: {$region->name}");

        return redirect()->route('regions.index')->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    public function destroy(Region $region)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'region',
                'model_id'     => $region->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => ['name' => $region->name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('regions.index')->with('success', 'تم إرسال طلب الحذف وهو بانتظار موافقة المسؤول.');
        }

        $name = $region->name;
        $region->delete();
        ActivityLogger::log('deleted', "حذف منطقة: {$name}");

        return redirect()->route('regions.index')->with('success', 'تم حذف المنطقة.');
    }
}

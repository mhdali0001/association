<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Sector;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{
    private function adminOnly(): void
    {
        abort_if(Auth::user()?->role !== 'admin', 403);
    }

    public function index()
    {
        $this->adminOnly();
        $sectors = Sector::withCount('members')->orderBy('name')->get();
        return view('sectors.index', compact('sectors'));
    }

    public function show(Sector $sector)
    {
        $this->adminOnly();
        $members = $sector->members()
            ->with(['verificationStatus', 'finalStatus', 'region'])
            ->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC')
            ->paginate(50)
            ->withQueryString();
        $allSectors = Sector::active()->orderBy('name')->get();
        return view('sectors.show', compact('sector', 'members', 'allSectors'));
    }

    public function store(Request $request)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name',
        ]);
        Sector::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة قطاع: {$data['name']}");
        return redirect()->route('sectors.index')->with('success', 'تمت إضافة القطاع بنجاح.');
    }

    public function update(Request $request, Sector $sector)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name,' . $sector->id,
        ]);
        $sector->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل قطاع: {$sector->name}");
        return redirect()->route('sectors.index')->with('success', 'تم تحديث القطاع بنجاح.');
    }

    public function destroy(Sector $sector)
    {
        $this->adminOnly();
        $name = $sector->name;
        $sector->delete();
        ActivityLogger::log('deleted', "حذف قطاع: {$name}");
        return redirect()->route('sectors.index')->with('success', 'تم حذف القطاع.');
    }
}

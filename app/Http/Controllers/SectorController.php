<?php

namespace App\Http\Controllers;

use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\PendingChange;
use App\Models\Region;
use App\Models\Sector;
use App\Models\VerificationStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{
    private function adminOnly(): void
    {
        abort_if(Auth::user()?->role !== 'admin', 403);
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'sector',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return response()->json([
                'pending' => true,
                'message' => 'تم إرسال طلب إضافة القطاع "' . $data['name'] . '" — بانتظار موافقة المسؤول.',
            ]);
        }

        $sector = Sector::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة قطاع: {$data['name']}");

        return response()->json(['id' => $sector->id, 'name' => $sector->name]);
    }

    public function index()
    {
        $this->adminOnly();
        $sectors = Sector::withCount(['members', 'regions'])->orderBy('name')->get();
        return view('sectors.index', compact('sectors'));
    }

    public function show(Request $request, Sector $sector)
    {
        $this->adminOnly();

        $search     = trim($request->get('search', ''));
        $regionId   = $request->get('region_id', '');
        $vsId       = $request->get('verification_status_id', '');
        $fsId       = $request->get('final_status_id', '');

        $query = $sector->members()->with(['verificationStatus', 'finalStatus', 'region']);

        if ($search !== '') {
            $query->where(fn($q) => $q->where('full_name', 'like', "%{$search}%")
                                      ->orWhere('dossier_number', 'like', "%{$search}%")
                                      ->orWhere('phone', 'like', "%{$search}%"));
        }
        if ($regionId !== '') $query->where('region_id', $regionId);
        if ($vsId     !== '') $query->where('verification_status_id', $vsId);
        if ($fsId     !== '') $query->where('final_status_id', $fsId);

        $members = $query->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC')
                         ->paginate(50)
                         ->withQueryString();

        $allSectors          = Sector::active()->orderBy('name')->get();
        $sectorRegions       = $sector->regions()->orderBy('name')->get();
        $availableRegions    = Region::whereNull('sector_id')->orderBy('name')->get();
        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatuses       = FinalStatus::active()->orderBy('name')->get();

        $hasFilters = $search !== '' || $regionId !== '' || $vsId !== '' || $fsId !== '';

        return view('sectors.show', compact(
            'sector', 'members', 'allSectors',
            'sectorRegions', 'availableRegions',
            'verificationStatuses', 'finalStatuses',
            'search', 'regionId', 'vsId', 'fsId', 'hasFilters'
        ));
    }

    public function updateRegions(Request $request, Sector $sector)
    {
        $this->adminOnly();

        $regionIds = array_filter((array) $request->input('region_ids', []));

        // Detach regions currently belonging to this sector that are not in the new list
        Region::where('sector_id', $sector->id)
              ->whereNotIn('id', $regionIds)
              ->update(['sector_id' => null]);

        // Attach new regions (only unassigned or already in this sector)
        if (!empty($regionIds)) {
            Region::whereIn('id', $regionIds)
                  ->where(fn($q) => $q->whereNull('sector_id')->orWhere('sector_id', $sector->id))
                  ->update(['sector_id' => $sector->id]);
        }

        ActivityLogger::log('updated', "تحديث مناطق القطاع: {$sector->name}");
        return redirect()->route('sectors.show', $sector)->with('success', 'تم تحديث مناطق القطاع بنجاح.');
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

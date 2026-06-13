<?php

namespace App\Http\Controllers;

use App\Exports\CustomMembersExport;
use App\Http\Controllers\Concerns\FiltersMembersQuery;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomExportController extends Controller
{
    use FiltersMembersQuery;

    public function show()
    {
        $groups = CustomMembersExport::groups();
        return view('members.custom-export', compact('groups'));
    }

    public function download(Request $request)
    {
        $allKeys  = array_keys(CustomMembersExport::allColumns());
        $selected = array_values(array_intersect($allKeys, (array) $request->get('columns', [])));

        if (empty($selected)) {
            return back()->with('error', 'يرجى اختيار عمود واحد على الأقل.');
        }

        $query = $this->buildFilteredQuery($request)
            ->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC');

        ActivityLogger::log('exported', 'تصدير مخصص: ' . count($selected) . ' عمود، ' . $query->count() . ' عضو');

        $filename = 'تصدير-مخصص-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new CustomMembersExport($query, $selected), $filename);
    }
}

<?php

namespace App\Http\Controllers;

use App\Imports\GenderImport;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GenderImportController extends Controller
{
    public function show()
    {
        return view('members.import-gender');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'يرجى اختيار ملف.',
            'file.mimes'    => 'يجب أن يكون الملف من نوع xlsx أو xls أو csv.',
            'file.max'      => 'حجم الملف لا يتجاوز 10 ميغابايت.',
        ]);

        $import = new GenderImport();
        Excel::import($import, $request->file('file'));

        ActivityLogger::log('updated', 'استيراد بيانات الجنس من Excel — ' . count($import->updated) . ' عضو محدّث');

        return redirect()->route('members.import-gender.show')
            ->with('import_updated', $import->updated)
            ->with('import_skipped', $import->skipped)
            ->with('import_errors',  $import->errors);
    }
}

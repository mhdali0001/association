<?php

namespace App\Http\Controllers;

use App\Models\ImportResult;
use App\Services\ActivityLogger;
use App\Services\MemberRowImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class MemberImportController extends Controller
{
    private const CHUNK_SIZE = 300;
    private const MAX_ROWS   = 1600;

    public function show()
    {
        return view('members.import');
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ], [
            'file.required' => 'يرجى اختيار ملف.',
            'file.mimes'    => 'صيغة الملف غير مدعومة. يُقبل فقط: xlsx, xls, csv.',
        ]);

        $file      = $request->file('file');
        $path      = $file->store('imports');
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath  = Storage::disk('local')->path($path);

        $totalRows = min($this->countRows($filePath, $extension), self::MAX_ROWS);

        $importResult = ImportResult::create([
            'user_id'    => Auth::id(),
            'status'     => 'pending',
            'file_path'  => $path,
            'file_ext'   => $extension,
            'total_rows' => $totalRows,
        ]);

        ActivityLogger::log('created', 'بدأ استيراد أعضاء من ملف Excel');

        return redirect()->route('members.import.status', $importResult->id);
    }

    public function chunk(Request $request, ImportResult $importResult)
    {
        if (in_array($importResult->status, ['done', 'failed'])) {
            return response()->json(['done' => true]);
        }

        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $offset   = (int) $request->input('offset', 0);
        $filePath = Storage::disk('local')->path($importResult->file_path);

        [$headers, $chunkRows] = $this->readChunk($filePath, $importResult->file_ext, $offset, self::CHUNK_SIZE);

        $importer = new MemberRowImporter($importResult->user_id);
        $imported = [];
        $errors   = [];

        foreach ($chunkRows as $i => $row) {
            $rowNum  = $offset + $i + 2; // +2: header row + 1-based
            $padded  = $row + array_fill(0, count($headers), null);
            $rowData = array_combine($headers, array_slice($padded, 0, count($headers)));
            $result  = $importer->processRow($rowData, $rowNum);

            if ($result['imported']) $imported[] = $result['imported'];
            if ($result['error'])    $errors[]   = $result['error'];
        }

        $processedRows = $offset + count($chunkRows);
        $done          = count($chunkRows) < self::CHUNK_SIZE || $processedRows >= $importResult->total_rows;

        $importResult->update([
            'processed_rows' => $processedRows,
            'imported'       => array_merge($importResult->imported ?? [], $imported),
            'errors'         => array_merge($importResult->errors   ?? [], $errors),
            'status'         => $done ? 'done' : 'pending',
        ]);

        if ($done) {
            Storage::disk('local')->delete($importResult->file_path);
            $fresh = $importResult->fresh();
            ActivityLogger::log('created',
                'اكتمل الاستيراد — مُستورد: ' . count($fresh->imported ?? []) .
                ', أخطاء: ' . count($fresh->errors ?? [])
            );
        }

        return response()->json([
            'imported'       => $imported,
            'errors'         => $errors,
            'processed_rows' => $processedRows,
            'total_rows'     => $importResult->total_rows,
            'next_offset'    => $processedRows,
            'done'           => $done,
        ]);
    }

    public function status(Request $request, ImportResult $importResult)
    {
        if ($request->ajax()) {
            return response()->json(['status' => $importResult->status]);
        }

        return view('members.import-status', compact('importResult'));
    }

    public function template()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="members_template.csv"',
        ];

        $columns = [
            'الاسم_الكامل', 'رقم_الهوية', 'رقم_الهاتف', 'رقم_الملف',
            'العمر', 'الجنس', 'الحالة_الاجتماعية', 'نوع_المرض',
            'العنوان', 'اسم_الأم', 'الوظيفة', 'وضع_السكن', 'عدد_المعالين',
            'المندوب', 'حالة_التحقق', 'الشبكة', 'الجمعية',
            'منتسب_لجمعية_أخرى', 'وصف_الحالات_الخاصة', 'حساب_شام_كاش',
            'الآيبان', 'الباركود',
            'درجة_العمل', 'درجة_السكن', 'درجة_المعالين', 'درجة_حالة_المعيل', 'درجة_المرض', 'درجة_الحالات_الخاصة',
        ];

        $example = [
            'محمد أحمد علي', '1234567890', '0911234567', 'D-001',
            '45', 'ذكر', 'متزوج', 'ضغط الدم',
            'دمشق - باب توما', 'فاطمة حسن', 'موظف', 'مستأجر', '3',
            'أحمد محمد', 'نشط', 'MTN', 'جمعية النور',
            'لا', 'حالة خاصة تستوجب المتابعة', 'نعم',
            'SA44 2000 0001 2345 6789 1234', '',
            '2', '3', '4', '1', '5', '1',
        ];

        $callback = function () use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Count data rows (excluding header) using streaming — no memory issues.
     */
    private function countRows(string $filePath, string $ext): int
    {
        $count     = 0;
        $firstRow  = true;
        $reader    = $this->makeReader($ext, $filePath);
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) { $firstRow = false; continue; } // skip header
                $count++;
            }
            break;
        }

        $reader->close();
        return $count;
    }

    /**
     * Read $limit data rows starting at $offset (0-based, after header).
     * Returns [$headers, $rows] — each row is a plain array of scalar values.
     */
    private function readChunk(string $filePath, string $ext, int $offset, int $limit): array
    {
        $headers   = [];
        $dataRows  = [];
        $dataIndex = 0;
        $firstRow  = true;

        $reader = $this->makeReader($ext, $filePath);
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $values = array_map(fn($cell) => $cell->getValue(), $row->getCells());

                if ($firstRow) {
                    $headers  = array_map(fn($h) => trim((string) ($h ?? '')), $values);
                    $firstRow = false;
                    continue;
                }

                if ($dataIndex < $offset) {
                    $dataIndex++;
                    continue;
                }

                if (count($dataRows) >= $limit) break;

                if (count(array_filter($values, fn($v) => $v !== null && $v !== '')) > 0) {
                    $dataRows[] = $values;
                }
                $dataIndex++;
            }
            break;
        }

        $reader->close();
        return [$headers, $dataRows];
    }

    private function makeReader(string $ext, string $filePath): XlsxReader|CsvReader
    {
        if ($ext === 'csv') {
            $options = new CsvOptions();
            $options->ENCODING = 'UTF-8';
            return new CsvReader($options);
        }

        return new XlsxReader();
    }
}

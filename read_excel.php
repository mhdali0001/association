<?php
ini_set('memory_limit', '2G');
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkFilter implements IReadFilter {
    public int $startRow = 1;
    public int $endRow   = 1;
    public function readCell($columnAddress, $row, $worksheetName = ''): bool {
        return $row === 1 || ($row >= $this->startRow && $row <= $this->endRow);
    }
}

$chunkFilter = new ChunkFilter();
$reader = IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(true);
$reader->setReadFilter($chunkFilter);

// First pass: get headers + count rows by reading a small chunk
$chunkFilter->startRow = 2;
$chunkFilter->endRow   = 2;
echo "Reading headers...\n";
$spreadsheet = $reader->load(__DIR__ . '/الملف الجاهز للداتا للرفع.xlsx');
$sheet = $spreadsheet->getActiveSheet();

$headers = [];
$col = 'A';
while (true) {
    $val = $sheet->getCell($col . '1')->getValue();
    if ($val !== null && $val !== '') {
        $headers[$col] = trim((string)$val);
    }
    if ($col === $sheet->getHighestColumn()) break;
    $col++;
}
$spreadsheet->disconnectWorksheets(); unset($spreadsheet);

echo "Headers:\n";
foreach ($headers as $c => $h) echo "  $c: $h\n";

// Read all data in chunks of 100
$allRows = [];
$chunkSize = 100;
$startRow = 2;

echo "\nReading data in chunks...\n";
while (true) {
    $chunkFilter->startRow = $startRow;
    $chunkFilter->endRow   = $startRow + $chunkSize - 1;

    $spreadsheet = $reader->load(__DIR__ . '/الملف الجاهز للداتا للرفع.xlsx');
    $sheet = $spreadsheet->getActiveSheet();

    $chunkRows = [];
    for ($row = $startRow; $row <= $chunkFilter->endRow; $row++) {
        $rowData = [];
        $hasData = false;
        foreach ($headers as $col => $header) {
            $val = $sheet->getCell($col . $row)->getValue();
            $rowData[$header] = $val;
            if ($val !== null && $val !== '') $hasData = true;
        }
        if (!$hasData) break;
        $chunkRows[] = $rowData;
    }

    $spreadsheet->disconnectWorksheets(); unset($spreadsheet);

    if (empty($chunkRows)) break;
    $allRows = array_merge($allRows, $chunkRows);
    echo "  Read up to row " . ($startRow + count($chunkRows) - 1) . " (total: " . count($allRows) . ")\n";
    if (count($chunkRows) < $chunkSize) break;
    $startRow += $chunkSize;
    gc_collect_cycles();
}

echo "\nTotal rows: " . count($allRows) . "\n";

// Show sample
foreach (array_slice($allRows, 0, 2) as $i => $row) {
    echo "\n--- Row " . ($i + 2) . " ---\n";
    foreach ($row as $k => $v) {
        if ($v !== null && $v !== '') echo "  $k: $v\n";
    }
}

// Save to JSON
file_put_contents(__DIR__ . '/excel_data.json', json_encode($allRows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\nSaved to excel_data.json\n";

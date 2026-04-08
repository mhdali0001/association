<?php
require 'vendor/autoload.php';

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('payment.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo "Total rows: " . count($rows) . PHP_EOL;
echo "=== First 5 rows ===" . PHP_EOL;
foreach (array_slice($rows, 0, 5) as $i => $row) {
    echo "Row $i: " . json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

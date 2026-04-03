<?php
ini_set('memory_limit', '256M');

$xlsxPath = __DIR__ . '/الملف الجاهز للداتا للرفع.xlsx';
$tempDir = sys_get_temp_dir() . '/xlsx_' . uniqid();
mkdir($tempDir, 0777, true);

$zip = new ZipArchive();
$zip->open($xlsxPath);
$zip->extractTo($tempDir);
$zip->close();
echo "Extracted.\n";

// Read shared strings
$sharedStrings = [];
$ssPath = $tempDir . '/xl/sharedStrings.xml';
if (file_exists($ssPath)) {
    $xml = simplexml_load_file($ssPath);
    foreach ($xml->si as $si) {
        if (isset($si->t)) {
            $sharedStrings[] = (string)$si->t;
        } else {
            $text = '';
            foreach ($si->r as $r) $text .= (string)$r->t;
            $sharedStrings[] = $text;
        }
    }
    echo "Shared strings: " . count($sharedStrings) . "\n";
    unset($xml);
}

function colLetterToIndex(string $cell): int {
    preg_match('/([A-Z]+)/', strtoupper($cell), $m);
    $col = $m[1];
    $index = 0;
    for ($i = 0; $i < strlen($col); $i++) {
        $index = $index * 26 + (ord($col[$i]) - 64);
    }
    return $index - 1;
}

$wsPath = $tempDir . '/xl/worksheets/sheet1.xml';
$xmlReader = new XMLReader();
$xmlReader->open($wsPath);

$headers = [];
$outFile = fopen(__DIR__ . '/excel_data.json', 'w');
fwrite($outFile, "[\n");
$firstRow = true;
$totalRows = 0;

$currentRowNum = 0;
$currentRow = [];
$currentCell = '';
$currentType = '';
$inValue = false;
$cellValue = '';

while ($xmlReader->read()) {
    if ($xmlReader->nodeType === XMLReader::ELEMENT) {
        if ($xmlReader->name === 'row') {
            $currentRowNum = (int)$xmlReader->getAttribute('r');
            $currentRow = [];
        } elseif ($xmlReader->name === 'c') {
            $currentCell = $xmlReader->getAttribute('r');
            $currentType = $xmlReader->getAttribute('t') ?? '';
            $cellValue = '';
            $inValue = false;
        } elseif ($xmlReader->name === 'v') {
            $inValue = true;
            $cellValue = '';
        }
    } elseif ($xmlReader->nodeType === XMLReader::TEXT && $inValue) {
        $cellValue .= $xmlReader->value;
    } elseif ($xmlReader->nodeType === XMLReader::END_ELEMENT) {
        if ($xmlReader->name === 'v') {
            $inValue = false;
            if ($currentType === 's') {
                $val = $sharedStrings[(int)$cellValue] ?? '';
            } elseif (is_numeric($cellValue)) {
                $val = $cellValue + 0;
            } else {
                $val = $cellValue;
            }
            $currentRow[colLetterToIndex($currentCell)] = $val;
        } elseif ($xmlReader->name === 'row') {
            ksort($currentRow);
            if ($currentRowNum === 1) {
                $headers = array_values($currentRow);
                echo "Headers: " . implode(' | ', $headers) . "\n";
            } else {
                $rowAssoc = [];
                $hasData = false;
                foreach ($headers as $idx => $header) {
                    $v = $currentRow[$idx] ?? null;
                    $rowAssoc[$header] = $v;
                    if ($v !== null && $v !== '') $hasData = true;
                }
                // Only include rows with dossier_number AND full_name
                $dosNum  = $rowAssoc['dossier_number'] ?? null;
                $fullName = trim((string)($rowAssoc['full_name'] ?? ''));
                if ($dosNum !== null && $dosNum !== '' && is_numeric($dosNum) && $fullName !== '') {
                    if (!$firstRow) fwrite($outFile, ",\n");
                    fwrite($outFile, json_encode($rowAssoc, JSON_UNESCAPED_UNICODE));
                    $firstRow = false;
                    $totalRows++;
                    if ($totalRows % 200 === 0) echo "  Wrote $totalRows rows...\n";
                }
            }
        }
    }
}
$xmlReader->close();
fwrite($outFile, "\n]\n");
fclose($outFile);

echo "\nDone. Total: $totalRows rows saved to excel_data.json\n";

// Show 2 sample rows
$data = json_decode(file_get_contents(__DIR__ . '/excel_data.json'), true);
foreach (array_slice($data, 0, 2) as $i => $row) {
    echo "\n--- Row " . ($i+2) . " ---\n";
    foreach ($row as $k => $v) if ($v !== null && $v !== '') echo "  $k: $v\n";
}

<?php
ini_set('memory_limit', '512M');

/**
 * Column positions (0-based index) → English key mapping
 * Col 0 : dossier_number
 * Col 1 : full_name
 * Col 2 : age
 * Col 3 : mother_name
 * Col 4 : national_id
 * Col 5 : current_address
 * Col 6 : marital_status
 * Col 7 : disease_type
 * Col 8 : association_id
 * Col 9 : phone
 * Col 10: phone2 (ignored)
 * Col 11: delegate
 * Col 12: network
 * Col 13: dependent_status_score
 * Col 14: work_score
 * Col 15: housing_score
 * Col 16: dependents_score
 * Col 17: illness_score
 * Col 18: special_cases_score
 * Col 19: score          (formula =sum of above — may be null in readDataOnly)
 * Col 20: estimated_amount (formula =score*50000 — may be null)
 * Col 21: verification_status_id
 * Col 22: special_cases_description
 * Col 23: sham_cash_account
 * Col 24: barcode (ignored)
 */
$colMap = [
    0  => 'dossier_number',
    1  => 'full_name',
    2  => 'age',
    3  => 'mother_name',
    4  => 'national_id',
    5  => 'current_address',
    6  => 'marital_status',
    7  => 'disease_type',
    8  => 'association_id',
    9  => 'phone',
    // 10 => phone2 — skip
    11 => 'delegate',
    12 => 'network',
    13 => 'dependent_status_score',
    14 => 'work_score',
    15 => 'housing_score',
    16 => 'dependents_score',
    17 => 'illness_score',
    18 => 'special_cases_score',
    19 => 'score',
    20 => 'estimated_amount',
    21 => 'verification_status_id',
    22 => 'special_cases_description',
    23 => 'sham_cash_account',
];

$xlsxPath = __DIR__ . '/lassst.xlsx';
if (!file_exists($xlsxPath)) {
    die("File not found: $xlsxPath\n");
}

$tempDir = sys_get_temp_dir() . '/xlsx_lassst_' . uniqid();
mkdir($tempDir, 0777, true);

$zip = new ZipArchive();
$zip->open($xlsxPath);
$zip->extractTo($tempDir);
$zip->close();
echo "Extracted to $tempDir\n";

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
    echo "Shared strings loaded: " . count($sharedStrings) . "\n";
    unset($xml);
}

function colLetterToIndex(string $cell): int {
    preg_match('/([A-Z]+)/', strtoupper($cell), $m);
    $col   = $m[1];
    $index = 0;
    for ($i = 0; $i < strlen($col); $i++) {
        $index = $index * 26 + (ord($col[$i]) - 64);
    }
    return $index - 1; // 0-based
}

$wsPath    = $tempDir . '/xl/worksheets/sheet1.xml';
$xmlReader = new XMLReader();
$xmlReader->open($wsPath);

$outFile  = fopen(__DIR__ . '/excel_data_lassst.json', 'w');
fwrite($outFile, "[\n");
$firstRow  = true;
$totalRows = 0;

$currentRowNum = 0;
$currentRow    = [];
$currentCell   = '';
$currentType   = '';
$inValue       = false;
$cellValue     = '';

while ($xmlReader->read()) {
    if ($xmlReader->nodeType === XMLReader::ELEMENT) {
        if ($xmlReader->name === 'row') {
            $currentRowNum = (int)$xmlReader->getAttribute('r');
            $currentRow    = [];
        } elseif ($xmlReader->name === 'c') {
            $currentCell = $xmlReader->getAttribute('r');
            $currentType = $xmlReader->getAttribute('t') ?? '';
            $cellValue   = '';
            $inValue     = false;
        } elseif ($xmlReader->name === 'v') {
            $inValue   = true;
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
            // Skip header row (row 1)
            if ($currentRowNum === 1) continue;

            // Map positional columns → English keys
            $row = [];
            foreach ($colMap as $colIdx => $key) {
                $row[$key] = $currentRow[$colIdx] ?? null;
            }

            // Validate: need dossier_number (numeric) and full_name
            $dossier  = $row['dossier_number'];
            $fullName = trim((string)($row['full_name'] ?? ''));
            if ($dossier === null || !is_numeric($dossier) || $fullName === '') {
                continue;
            }

            // Compute score from components if formula cell was null/string
            $dep    = is_numeric($row['dependent_status_score'])  ? (int)$row['dependent_status_score']  : 0;
            $work   = is_numeric($row['work_score'])              ? (int)$row['work_score']              : 0;
            $house  = is_numeric($row['housing_score'])           ? (int)$row['housing_score']           : 0;
            $deps   = is_numeric($row['dependents_score'])        ? (int)$row['dependents_score']        : 0;
            $ill    = is_numeric($row['illness_score'])           ? (int)$row['illness_score']           : 0;
            $spec   = is_numeric($row['special_cases_score'])     ? (int)$row['special_cases_score']     : 0;
            $computed = $dep + $work + $house + $deps + $ill + $spec;

            $row['dependent_status_score']  = $dep;
            $row['work_score']              = $work;
            $row['housing_score']           = $house;
            $row['dependents_score']        = $deps;
            $row['illness_score']           = $ill;
            $row['special_cases_score']     = $spec;
            $row['score']                   = is_numeric($row['score']) ? (int)$row['score'] : $computed;

            // estimated_amount: formula =score*50000; seeder will divide by 100 → stored = score*500
            if (!is_numeric($row['estimated_amount'])) {
                $row['estimated_amount'] = $row['score'] * 50000;
            }

            if (!$firstRow) fwrite($outFile, ",\n");
            fwrite($outFile, json_encode($row, JSON_UNESCAPED_UNICODE));
            $firstRow = false;
            $totalRows++;

            if ($totalRows % 200 === 0) echo "  Wrote $totalRows rows...\n";
        }
    }
}

$xmlReader->close();
fwrite($outFile, "\n]\n");
fclose($outFile);

echo "\nDone. Total: $totalRows rows → excel_data_lassst.json\n";

// Show 3 sample rows
$data = json_decode(file_get_contents(__DIR__ . '/excel_data_lassst.json'), true);
foreach (array_slice($data, 0, 3) as $i => $row) {
    echo "\n--- Row " . ($i + 2) . " ---\n";
    foreach ($row as $k => $v) {
        if ($v !== null && $v !== '') echo "  $k: $v\n";
    }
}

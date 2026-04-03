<?php
$data = json_decode(file_get_contents(__DIR__ . '/excel_data.json'), true);
echo "Total rows: " . count($data) . "\n\n";

$cols = ['marital_status', 'verification_status_id', 'association_id', 'sham_cash_account', 'network', 'delegate'];
foreach ($cols as $col) {
    $values = array_count_values(array_map('trim', array_column($data, $col)));
    arsort($values);
    echo "=== $col ===\n";
    foreach ($values as $v => $c) {
        if ($v !== '') echo "  [$c] $v\n";
    }
    echo "\n";
}

// special_cases_description unique values (first 10)
echo "=== special_cases_description (non-empty) ===\n";
$descs = array_filter(array_map('trim', array_column($data, 'special_cases_description')));
$unique = array_unique($descs);
foreach (array_slice($unique, 0, 10) as $v) echo "  $v\n";
echo "  Total non-empty: " . count($descs) . "\n\n";

// estimated_amount range
$amounts = array_filter(array_column($data, 'estimated_amount'));
echo "=== estimated_amount ===\n";
echo "  Min: " . min($amounts) . "\n";
echo "  Max: " . max($amounts) . "\n";
echo "  Sample: " . implode(', ', array_slice($amounts, 0, 5)) . "\n\n";

// score range
$scores = array_filter(array_column($data, 'score'));
echo "=== score ===\n";
echo "  Sample: " . implode(', ', array_slice($scores, 0, 5)) . "\n";

<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\PaymentInfo;
use App\Models\PaymentInfoAI;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PaymentImportSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('payment.xlsx');

        if (!file_exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        $spreadsheet = IOFactory::load($path);
        $rows        = $spreadsheet->getActiveSheet()->toArray();

        // Remove header row
        $header = array_shift($rows);
        $this->command->info("Columns: " . implode(', ', $header));
        $this->command->info("Total data rows: " . count($rows));

        // Map column index by header name (case-insensitive)
        $colMap = [];
        foreach ($header as $i => $col) {
            $colMap[strtolower(trim((string)$col))] = $i;
        }

        $notFound  = 0;
        $inserted  = 0;
        $updated   = 0;

        foreach ($rows as $lineNo => $row) {
            $dossier = trim((string)($row[$colMap['dossier_number']] ?? ''));

            if ($dossier === '') {
                continue;
            }

            // Find member by dossier_number
            $member = Member::where('dossier_number', $dossier)->first();

            if (!$member) {
                $this->command->warn("Line " . ($lineNo + 2) . ": Member not found for dossier '{$dossier}'");
                $notFound++;
                continue;
            }

            // --- payment_info ---
            $iban          = trim((string)($row[$colMap['iban']]            ?? '')) ?: null;
            $recipientName = trim((string)($row[$colMap['recipient_name']]  ?? '')) ?: null;

            $existing = PaymentInfo::where('member_id', $member->id)->first();

            if ($existing) {
                $existing->update([
                    'iban'           => $iban,
                    'recipient_name' => $recipientName,
                ]);
                $updated++;
            } else {
                PaymentInfo::create([
                    'member_id'      => $member->id,
                    'iban'           => $iban,
                    'recipient_name' => $recipientName,
                ]);
                $inserted++;
            }

            // --- payment_info_ai ---
            $ibanAI          = trim((string)($row[$colMap['iban_ai']]            ?? '')) ?: null;
            $barcodeAI       = trim((string)($row[$colMap['barcode_ai']]         ?? '')) ?: null;
            $recipientNameAI = trim((string)($row[$colMap['recipient_name_ai']]  ?? '')) ?: null;

            $existingAI = PaymentInfoAI::where('member_id', $member->id)->first();

            if ($existingAI) {
                $existingAI->update([
                    'iban'           => $ibanAI,
                    'barcode'        => $barcodeAI,
                    'recipient_name' => $recipientNameAI,
                ]);
            } else {
                PaymentInfoAI::create([
                    'member_id'      => $member->id,
                    'iban'           => $ibanAI,
                    'barcode'        => $barcodeAI,
                    'recipient_name' => $recipientNameAI,
                ]);
            }
        }

        $this->command->info("Done! Inserted: {$inserted} | Updated: {$updated} | Not found: {$notFound}");
    }
}

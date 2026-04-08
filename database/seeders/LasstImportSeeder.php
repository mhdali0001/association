<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\MaritalStatus;
use App\Models\VerificationStatus;
use App\Models\Association;
use App\Models\MemberScore;

class LasstImportSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('excel_data_lassst.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('excel_data_lassst.json not found. Run read_lassst.php first.');
            return;
        }

        $rows = json_decode(file_get_contents($jsonPath), true);
        $this->command->info('Loaded ' . count($rows) . ' rows from excel_data_lassst.json');

        // Pre-load lookup maps
        $maritalMap      = MaritalStatus::pluck('id', 'name')->toArray();
        $verificationMap = VerificationStatus::pluck('id', 'name')->toArray();
        $associationMap  = Association::pluck('id', 'name')->toArray();

        $defaultColors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444','#14b8a6','#f97316','#a855f7'];
        $colorIdx = 0;

        $bar = $this->command->getOutput()->createProgressBar(count($rows));
        $bar->start();

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            $bar->advance();

            $dossierNum = trim((string)($row['dossier_number'] ?? ''));
            $fullName   = trim((string)($row['full_name'] ?? ''));

            if ($dossierNum === '' || $fullName === '') {
                $skipped++;
                continue;
            }

            // Skip if dossier_number already exists
            if (Member::where('dossier_number', $dossierNum)->exists()) {
                $skipped++;
                continue;
            }

            // Marital Status
            $maritalName = trim((string)($row['marital_status'] ?? ''));
            if ($maritalName !== '' && !isset($maritalMap[$maritalName])) {
                $ms = MaritalStatus::create(['name' => $maritalName, 'is_active' => true]);
                $maritalMap[$maritalName] = $ms->id;
            }

            // Verification Status
            $verName = trim((string)($row['verification_status_id'] ?? ''));
            if ($verName !== '' && !isset($verificationMap[$verName])) {
                $color = $defaultColors[$colorIdx % count($defaultColors)];
                $colorIdx++;
                $vs = VerificationStatus::create(['name' => $verName, 'color' => $color, 'is_active' => true]);
                $verificationMap[$verName] = $vs->id;
            }
            $verStatusId = $verName !== '' ? ($verificationMap[$verName] ?? null) : null;

            // Association
            $assocRaw      = trim((string)($row['association_id'] ?? ''));
            $otherAssoc    = false;
            $memberAssocId = null;

            if ($assocRaw === 'نعم' || $assocRaw === 'نعم ') {
                $otherAssoc = true;
            } elseif ($assocRaw !== '' && $assocRaw !== 'لا' && $assocRaw !== 'لا ') {
                if (!isset($associationMap[$assocRaw])) {
                    $a = Association::create(['name' => $assocRaw, 'is_active' => true]);
                    $associationMap[$assocRaw] = $a->id;
                }
                $memberAssocId = $associationMap[$assocRaw];
            }

            // Sham Cash Account
            $shamRaw  = trim((string)($row['sham_cash_account'] ?? ''));
            $shamCash = null;
            if ($shamRaw === 'تم') {
                $shamCash = 'done';
            } elseif ($shamRaw === 'يدوي') {
                $shamCash = 'manual';
            }

            // Estimated Amount (÷ 100)
            $rawAmount       = $row['estimated_amount'] ?? 0;
            $estimatedAmount = is_numeric($rawAmount) ? ($rawAmount / 100) : null;

            // Special Cases
            $specialDesc = trim((string)($row['special_cases_description'] ?? ''));
            $specialCase = $specialDesc !== '';

            // Network
            $network = trim((string)($row['network'] ?? ''));
            if (!in_array($network, ['MTN', 'SYRIATEL'])) $network = null;

            // Phone
            $phone = trim((string)($row['phone'] ?? ''));
            if ($phone === '' || $phone === '0') $phone = null;

            // Create Member
            $member = Member::create([
                'dossier_number'            => $dossierNum,
                'full_name'                 => $fullName,
                'age'                       => is_numeric($row['age'] ?? '') ? (int)$row['age'] : null,
                'mother_name'               => trim((string)($row['mother_name'] ?? '')) ?: null,
                'national_id'               => trim((string)($row['national_id'] ?? '')) ?: null,
                'current_address'           => trim((string)($row['current_address'] ?? '')) ?: null,
                'marital_status'            => $maritalName ?: null,
                'disease_type'              => trim((string)($row['disease_type'] ?? '')) ?: null,
                'other_association'         => $otherAssoc,
                'phone'                     => $phone,
                'delegate'                  => trim((string)($row['delegate'] ?? '')) ?: null,
                'network'                   => $network,
                'verification_status_id'    => $verStatusId,
                'special_cases'             => $specialCase,
                'special_cases_description' => $specialDesc ?: null,
                'score'                     => is_numeric($row['score'] ?? '') ? (int)$row['score'] : null,
                'estimated_amount'          => $estimatedAmount,
                'sham_cash_account'         => $shamCash,
            ]);

            // member_associations
            if ($memberAssocId) {
                DB::table('member_associations')->insertOrIgnore([
                    'member_id'      => $member->id,
                    'association_id' => $memberAssocId,
                ]);
            }

            // Member Scores
            $depScore          = (int)($row['dependent_status_score'] ?? 0);
            $workScore         = (int)($row['work_score'] ?? 0);
            $housingScore      = (int)($row['housing_score'] ?? 0);
            $dependentsScore   = (int)($row['dependents_score'] ?? 0);
            $illnessScore      = (int)($row['illness_score'] ?? 0);
            $specialCasesScore = (int)($row['special_cases_score'] ?? 0);
            $totalScore        = $depScore + $workScore + $housingScore + $dependentsScore + $illnessScore + $specialCasesScore;

            MemberScore::create([
                'member_id'              => $member->id,
                'dependent_status_score' => $depScore,
                'work_score'             => $workScore,
                'housing_score'          => $housingScore,
                'dependents_score'       => $dependentsScore,
                'illness_score'          => $illnessScore,
                'special_cases_score'    => $specialCasesScore,
                'total_score'            => $totalScore,
            ]);

            $inserted++;
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Done. Inserted: $inserted | Skipped (duplicate/empty): $skipped");
    }
}

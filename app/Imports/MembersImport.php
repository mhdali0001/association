<?php

namespace App\Imports;

use App\Models\Association;
use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\User;
use App\Models\VerificationStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public array $imported = [];
    public array $skipped  = [];
    public array $errors   = [];

    private ?int $defaultVerificationId;
    private Collection $usersMap;
    private Collection $verificationMap;
    private Collection $associationMap;
    private int $rowOffset = 2; // row 1 is header

    public function __construct(private ?int $actingUserId = null)
    {
        $this->defaultVerificationId = VerificationStatus::active()->first()?->id;
        $this->usersMap        = User::all()->keyBy(fn($u) => mb_strtolower(trim($u->name)));
        $this->verificationMap = VerificationStatus::all()->keyBy(fn($v) => mb_strtolower(trim($v->name)));
        $this->associationMap  = Association::all()->keyBy(fn($a) => mb_strtolower(trim($a->name)));
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $this->rowOffset + $index;

            $fullName   = trim($row['الاسم_الكامل']   ?? $row['full_name']   ?? '');
            $nationalId = trim($row['رقم_الهوية']     ?? $row['national_id'] ?? '');

            if ($fullName === '' && $nationalId === '') {
                continue;
            }

            if ($fullName === '') {
                $this->errors[] = "الصف {$rowNum}: الاسم الكامل مطلوب.";
                continue;
            }

            if ($nationalId === '') {
                $this->errors[] = "الصف {$rowNum} ({$fullName}): رقم الهوية مطلوب.";
                continue;
            }

            try {
                $repName = mb_strtolower(trim($row['المندوب'] ?? $row['representative'] ?? ''));
                $fallbackUserId = $this->actingUserId ?? Auth::id();
                $representativeId = $repName !== ''
                    ? ($this->usersMap->get($repName)?->id ?? $fallbackUserId)
                    : $fallbackUserId;

                $verName = mb_strtolower(trim($row['حالة_التحقق'] ?? $row['verification_status'] ?? ''));
                $verificationStatusId = $verName !== ''
                    ? ($this->verificationMap->get($verName)?->id ?? $this->defaultVerificationId)
                    : $this->defaultVerificationId;

                $otherAssocRaw  = trim($row['منتسب_لجمعية_أخرى'] ?? $row['other_association'] ?? '');
                $otherAssocLower = mb_strtolower($otherAssocRaw);
                $knownBoolValues = ['نعم', 'لا', 'yes', 'no', '1', '0', 'true', 'false', ''];

                if (!in_array($otherAssocLower, $knownBoolValues, true)) {
                    // Value is an association name — use it as association and force other_association = true
                    $otherAssocBool = true;
                    $assocName = $otherAssocLower;
                } else {
                    $otherAssocBool = in_array($otherAssocLower, ['نعم', 'yes', '1', 'true'], true);
                    $assocName = mb_strtolower(trim($row['الجمعية'] ?? $row['association'] ?? ''));
                }

                $shamCash = trim($row['حساب_شام_كاش'] ?? $row['sham_cash_account'] ?? '');
                $shamCashBool = in_array(mb_strtolower($shamCash), ['نعم', 'تم', 'yes', '1', 'true'], true);

                $networkRaw = strtoupper(trim($row['الشبكة'] ?? $row['network'] ?? ''));
                $network = in_array($networkRaw, ['MTN', 'SYRIATEL']) ? $networkRaw : null;

                $associationId = $assocName !== '' ? ($this->associationMap->get($assocName)?->id ?? null) : null;

                $member = Member::create([
                    'full_name'                 => $fullName,
                    'national_id'               => $nationalId,
                    'phone'                     => trim($row['رقم_الهاتف']         ?? $row['phone']           ?? '') ?: null,
                    'dossier_number'            => trim($row['رقم_الملف']          ?? $row['dossier_number']  ?? '') ?: null,
                    'age'                       => is_numeric($row['العمر']        ?? $row['age'] ?? '') ? (int)($row['العمر'] ?? $row['age']) : null,
                    'gender'                    => trim($row['الجنس']              ?? $row['gender']          ?? '') ?: null,
                    'marital_status'            => trim($row['الحالة_الاجتماعية'] ?? $row['marital_status']  ?? '') ?: null,
                    'disease_type'              => trim($row['نوع_المرض']          ?? $row['disease_type']    ?? '') ?: null,
                    'current_address'           => trim($row['العنوان']            ?? $row['current_address'] ?? '') ?: null,
                    'mother_name'               => trim($row['اسم_الأم']           ?? $row['mother_name']     ?? '') ?: null,
                    'job'                       => trim($row['الوظيفة']            ?? $row['job']             ?? '') ?: null,
                    'housing_status'            => trim($row['وضع_السكن']          ?? $row['housing_status']  ?? '') ?: null,
                    'dependents_count'          => is_numeric($row['عدد_المعالين'] ?? $row['dependents_count'] ?? '') ? (int)($row['عدد_المعالين'] ?? $row['dependents_count']) : null,
                    'network'                   => $network,
                    'other_association'         => $otherAssocBool,
                    'special_cases_description' => trim($row['وصف_الحالات_الخاصة'] ?? $row['special_cases_description'] ?? '') ?: null,
                    'sham_cash_account'         => $shamCashBool,
                    'representative_id'         => $representativeId,
                    'verification_status_id'    => $verificationStatusId,
                    'association_id'            => $associationId,
                    'score'                     => 0,
                    'estimated_amount'          => 0,
                ]);

                $workScore            = is_numeric($row['درجة_العمل']             ?? $row['work_score']             ?? '') ? (int)($row['درجة_العمل']             ?? $row['work_score'])             : 0;
                $housingScore         = is_numeric($row['درجة_السكن']             ?? $row['housing_score']          ?? '') ? min(4, (int)($row['درجة_السكن']     ?? $row['housing_score']))          : 0;
                $dependentsScore      = is_numeric($row['درجة_المعالين']          ?? $row['dependents_score']       ?? '') ? (int)($row['درجة_المعالين']          ?? $row['dependents_score'])       : 0;
                $dependentStatusScore = is_numeric($row['درجة_حالة_المعيل']      ?? $row['dependent_status_score'] ?? '') ? min(2, (int)($row['درجة_حالة_المعيل'] ?? $row['dependent_status_score'])) : 0;
                $illnessScore         = is_numeric($row['درجة_المرض']             ?? $row['illness_score']          ?? '') ? (int)($row['درجة_المرض']             ?? $row['illness_score'])          : 0;
                $specialScore         = is_numeric($row['درجة_الحالات_الخاصة']   ?? $row['special_cases_score']    ?? '') ? (int)($row['درجة_الحالات_الخاصة']   ?? $row['special_cases_score'])    : 0;

                MemberScore::create([
                    'member_id'              => $member->id,
                    'work_score'             => $workScore,
                    'housing_score'          => $housingScore,
                    'dependents_score'       => $dependentsScore,
                    'dependent_status_score' => $dependentStatusScore,
                    'illness_score'          => $illnessScore,
                    'special_cases_score'    => $specialScore,
                    'total_score'            => $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore,
                ]);

                PaymentInfo::create([
                    'member_id' => $member->id,
                    'iban'      => trim($row['الآيبان'] ?? $row['iban'] ?? '') ?: null,
                    'barcode'   => trim($row['الباركود'] ?? $row['barcode'] ?? '') ?: null,
                ]);

                $this->imported[] = $fullName;
            } catch (\Throwable $e) {
                $this->errors[] = "الصف {$rowNum} ({$fullName}): {$e->getMessage()}";
            }
        }

        $this->rowOffset += $rows->count();
    }
}

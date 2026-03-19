<?php

namespace App\Services;

use App\Models\Association;
use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\User;
use App\Models\VerificationStatus;
use Illuminate\Support\Collection;

class MemberRowImporter
{
    private ?int $defaultVerificationId;
    private Collection $usersMap;
    private Collection $verificationMap;
    private Collection $associationMap;

    public function __construct(private ?int $actingUserId = null)
    {
        $this->defaultVerificationId = VerificationStatus::active()->first()?->id;
        $this->usersMap        = User::all()->keyBy(fn($u) => mb_strtolower(trim($u->name)));
        $this->verificationMap = VerificationStatus::all()->keyBy(fn($v) => mb_strtolower(trim($v->name)));
        $this->associationMap  = Association::all()->keyBy(fn($a) => mb_strtolower(trim($a->name)));
    }

    /**
     * @return array{imported: string|null, error: string|null}
     */
    public function processRow(array $row, int $rowNum): array
    {
        $fullName   = trim($row['الاسم_الكامل']   ?? $row['full_name']   ?? '');
        $nationalId = trim($row['رقم_الهوية']     ?? $row['national_id'] ?? '');

        if ($fullName === '' && $nationalId === '') {
            return ['imported' => null, 'error' => null];
        }

        if ($fullName === '') {
            return ['imported' => null, 'error' => "الصف {$rowNum}: الاسم الكامل مطلوب."];
        }

        try {
            $repName = mb_strtolower(trim($row['المندوب'] ?? $row['representative'] ?? ''));
            $representativeId = $repName !== ''
                ? ($this->usersMap->get($repName)?->id ?? $this->actingUserId)
                : $this->actingUserId;

            $verName = mb_strtolower(trim($row['حالة_التحقق'] ?? $row['verification_status'] ?? ''));
            $verificationStatusId = $verName !== ''
                ? ($this->verificationMap->get($verName)?->id ?? $this->defaultVerificationId)
                : $this->defaultVerificationId;

            $otherAssocRaw   = trim($row['منتسب_لجمعية_أخرى'] ?? $row['other_association'] ?? '');
            $otherAssocLower = mb_strtolower($otherAssocRaw);
            $knownBoolValues = ['نعم', 'لا', 'yes', 'no', '1', '0', 'true', 'false', ''];

            if ($otherAssocRaw !== '' && !in_array($otherAssocLower, $knownBoolValues, true)) {
                // Value is an association name — use it and force other_association = true
                $otherAssocBool = true;
                $assocName      = $otherAssocLower;
            } else {
                $otherAssocBool = in_array($otherAssocLower, ['نعم', 'yes', '1', 'true'], true);
                $assocName      = mb_strtolower(trim($row['الجمعية'] ?? $row['association'] ?? ''));
            }

            $shamCash     = trim($row['حساب_شام_كاش'] ?? $row['sham_cash_account'] ?? '');
            $shamCashBool = in_array(mb_strtolower($shamCash), ['نعم', 'تم', 'yes', '1', 'true'], true);

            $networkRaw = strtoupper(trim($row['الشبكة'] ?? $row['network'] ?? ''));
            $network    = in_array($networkRaw, ['MTN', 'SYRIATEL']) ? $networkRaw : null;

            $associationId = $assocName !== '' ? ($this->associationMap->get($assocName)?->id ?? null) : null;

            $delegate = trim($row['مندوب_خارجي'] ?? $row['delegate'] ?? '') ?: null;

            $member = Member::create([
                'full_name'                 => $fullName,
                'national_id'               => $nationalId,
                'phone'                     => trim($row['رقم_الهاتف']         ?? $row['phone']           ?? '') ?: null,
                'dossier_number'            => trim($row['رقم_الملف']          ?? $row['dossier_number']  ?? '') ?: null,
                'age'                       => $this->toInt($row['العمر'] ?? $row['age'] ?? null),
                'gender'                    => trim($row['الجنس']              ?? $row['gender']          ?? '') ?: null,
                'marital_status'            => trim($row['الحالة_الاجتماعية'] ?? $row['marital_status']  ?? '') ?: null,
                'disease_type'              => trim($row['نوع_المرض']          ?? $row['disease_type']    ?? '') ?: null,
                'current_address'           => trim($row['العنوان']            ?? $row['current_address'] ?? '') ?: null,
                'mother_name'               => trim($row['اسم_الأم']           ?? $row['mother_name']     ?? '') ?: null,
                'job'                       => trim($row['الوظيفة']            ?? $row['job']             ?? '') ?: null,
                'housing_status'            => trim($row['وضع_السكن']          ?? $row['housing_status']  ?? '') ?: null,
                'dependents_count'          => $this->toInt($row['عدد_المعالين'] ?? $row['dependents_count'] ?? null),
                'network'                   => $network,
                'other_association'         => $otherAssocBool,
                'special_cases_description' => trim($row['وصف_الحالات_الخاصة'] ?? $row['special_cases_description'] ?? '') ?: null,
                'sham_cash_account'         => $shamCashBool,
                'representative_id'         => $representativeId,
                'delegate'                  => $delegate,
                'verification_status_id'    => $verificationStatusId,
                'association_id'            => $associationId,
            ]);

            $workScore            = min(2,  $this->toInt($row['درجة_العمل']           ?? $row['work_score']             ?? null) ?? 0);
            $housingScore         = min(2,  $this->toInt($row['درجة_السكن']           ?? $row['housing_score']          ?? null) ?? 0);
            $dependentsScore      = min(20, $this->toInt($row['درجة_المعالين']        ?? $row['dependents_score']       ?? null) ?? 0);
            $dependentStatusScore = min(2,  $this->toInt($row['درجة_حالة_المعيل']    ?? $row['dependent_status_score'] ?? null) ?? 0);
            $illnessScore         = min(5,  $this->toInt($row['درجة_المرض']           ?? $row['illness_score']          ?? null) ?? 0);
            $specialScore         = min(10, $this->toInt($row['درجة_الحالات_الخاصة'] ?? $row['special_cases_score']    ?? null) ?? 0);
            $totalScore           = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;

            $member->update([
                'score'            => $totalScore,
                'estimated_amount' => $totalScore * 500,
            ]);

            MemberScore::create([
                'member_id'              => $member->id,
                'work_score'             => $workScore,
                'housing_score'          => $housingScore,
                'dependents_score'       => $dependentsScore,
                'dependent_status_score' => $dependentStatusScore,
                'illness_score'          => $illnessScore,
                'special_cases_score'    => $specialScore,
                'total_score'            => $totalScore,
            ]);

            PaymentInfo::create([
                'member_id' => $member->id,
                'iban'      => trim($row['الآيبان'] ?? $row['iban']     ?? '') ?: null,
                'barcode'   => trim($row['الباركود'] ?? $row['barcode'] ?? '') ?: null,
            ]);

            return ['imported' => $fullName, 'error' => null];
        } catch (\Throwable $e) {
            return ['imported' => null, 'error' => "الصف {$rowNum} ({$fullName}): {$e->getMessage()}"];
        }
    }

    /**
     * Convert any cell value to int — handles int, float, numeric strings,
     * strings with extra text (e.g. "45 سنة"), and ignores dates/objects.
     */
    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value instanceof \DateTimeInterface) {
            return null;
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return (int) $value;
        }
        $str = trim((string) $value);
        if (is_numeric($str)) {
            return (int) $str;
        }
        // Extract leading digits (e.g. "45 سنة" → 45)
        if (preg_match('/^\d+/', $str, $m)) {
            return (int) $m[0];
        }
        return null;
    }
}

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

            $otherAssoc     = trim($row['منتسب_لجمعية_أخرى'] ?? $row['other_association'] ?? '');
            $otherAssocBool = in_array(mb_strtolower($otherAssoc), ['نعم', 'yes', '1', 'true'], true);

            $shamCash     = trim($row['حساب_شام_كاش'] ?? $row['sham_cash_account'] ?? '');
            $shamCashBool = in_array(mb_strtolower($shamCash), ['نعم', 'yes', '1', 'true'], true);

            $networkRaw = strtoupper(trim($row['الشبكة'] ?? $row['network'] ?? ''));
            $network    = in_array($networkRaw, ['MTN', 'SYRIATEL']) ? $networkRaw : null;

            $assocName     = mb_strtolower(trim($row['الجمعية'] ?? $row['association'] ?? ''));
            $associationId = $assocName !== '' ? ($this->associationMap->get($assocName)?->id ?? null) : null;

            $delegate = trim($row['مندوب_خارجي'] ?? $row['delegate'] ?? '') ?: null;

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
                'delegate'                  => $delegate,
                'verification_status_id'    => $verificationStatusId,
                'association_id'            => $associationId,
            ]);

            $workScore            = is_numeric($row['درجة_العمل']           ?? $row['work_score']             ?? '') ? min(2,  (int)($row['درجة_العمل']           ?? $row['work_score']))             : 0;
            $housingScore         = is_numeric($row['درجة_السكن']           ?? $row['housing_score']          ?? '') ? min(2,  (int)($row['درجة_السكن']           ?? $row['housing_score']))          : 0;
            $dependentsScore      = is_numeric($row['درجة_المعالين']        ?? $row['dependents_score']       ?? '') ? min(20, (int)($row['درجة_المعالين']        ?? $row['dependents_score']))       : 0;
            $dependentStatusScore = is_numeric($row['درجة_حالة_المعيل']    ?? $row['dependent_status_score'] ?? '') ? min(2,  (int)($row['درجة_حالة_المعيل']    ?? $row['dependent_status_score'])) : 0;
            $illnessScore         = is_numeric($row['درجة_المرض']           ?? $row['illness_score']          ?? '') ? min(5,  (int)($row['درجة_المرض']           ?? $row['illness_score']))          : 0;
            $specialScore         = is_numeric($row['درجة_الحالات_الخاصة'] ?? $row['special_cases_score']    ?? '') ? min(10, (int)($row['درجة_الحالات_الخاصة'] ?? $row['special_cases_score']))    : 0;
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
}

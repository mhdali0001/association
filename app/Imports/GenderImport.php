<?php

namespace App\Imports;

use App\Models\Member;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenderImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public array $updated = [];
    public array $skipped = [];
    public array $errors  = [];

    private int $rowOffset = 2;

    public function chunkSize(): int { return 500; }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $this->rowOffset + $index;

            $dossier    = trim($row['رقم_الملف']   ?? $row['dossier_number'] ?? $row['رقم الملف'] ?? '');
            $nationalId = trim($row['رقم_الهوية']  ?? $row['national_id']   ?? $row['رقم الهوية'] ?? '');
            $phone      = trim($row['رقم_الهاتف_الثاني'] ?? $row['رقم_الهاتف2'] ?? $row['phone2'] ?? $row['رقم الهاتف الثاني'] ?? '');
            $rawGender  = trim($row['الجنس']        ?? $row['gender']        ?? '');

            if ($dossier === '' && $nationalId === '' && $phone === '') {
                $this->errors[] = "الصف {$rowNum}: لا يوجد رقم ملف أو رقم هوية أو رقم هاتف.";
                continue;
            }

            if ($rawGender === '') {
                $this->skipped[] = "الصف {$rowNum}: لا توجد قيمة للجنس — تم التخطي.";
                continue;
            }

            $gender = $this->normalizeGender($rawGender);
            if ($gender === null) {
                $this->skipped[] = "الصف {$rowNum}: قيمة الجنس غير معروفة «{$rawGender}» — تم التخطي.";
                continue;
            }

            try {
                $member = null;

                if ($dossier !== '') {
                    $member = Member::where('dossier_number', $dossier)->first();
                }

                if (!$member && $nationalId !== '') {
                    $member = Member::where('national_id', $nationalId)->first();
                }

                if (!$member && $phone !== '') {
                    $member = Member::where('phone2', $phone)->first();
                }

                if (!$member) {
                    $id = $dossier ?: $nationalId ?: $phone;
                    $this->skipped[] = "الصف {$rowNum}: لم يُعثر على عضو بالمعرّف ({$id}).";
                    continue;
                }

                $member->update(['gender' => $gender]);
                $this->updated[] = "{$member->full_name} ← {$gender}";
            } catch (\Throwable $e) {
                $this->errors[] = "الصف {$rowNum}: {$e->getMessage()}";
            }
        }

        $this->rowOffset += $rows->count();
    }

    private function normalizeGender(string $value): ?string
    {
        $v = mb_strtolower(trim($value));

        if (in_array($v, ['ذكر', 'male', 'm', 'ذ', '1'])) {
            return 'ذكر';
        }

        if (in_array($v, ['أنثى', 'انثى', 'female', 'f', 'أ', 'ا', '2'])) {
            return 'أنثى';
        }

        return null;
    }
}

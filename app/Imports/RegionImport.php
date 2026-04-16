<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Region;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegionImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public array $updated  = [];
    public array $skipped  = [];
    public array $errors   = [];
    public array $created  = []; // regions created during this import

    private int $rowOffset = 2;

    /** @var array<string, int> region name → id cache */
    private array $regionCache = [];

    public function chunkSize(): int { return 500; }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $this->rowOffset + $index;

            $dossier    = trim($row['رقم_الملف']          ?? $row['dossier_number'] ?? $row['رقم الملف']          ?? '');
            $nationalId = trim($row['رقم_الهوية']         ?? $row['national_id']   ?? $row['رقم الهوية']         ?? '');
            $phone      = trim($row['رقم_الهاتف_الثاني']  ?? $row['phone2']        ?? $row['رقم الهاتف الثاني']  ?? $row['رقم_الهاتف2'] ?? '');
            $regionName = trim($row['المنطقة']             ?? $row['region']        ?? '');

            if ($dossier === '' && $nationalId === '' && $phone === '') {
                $this->errors[] = "الصف {$rowNum}: لا يوجد رقم ملف أو رقم هوية أو رقم هاتف.";
                continue;
            }

            if ($regionName === '') {
                $this->skipped[] = "الصف {$rowNum}: لا توجد قيمة للمنطقة — تم التخطي.";
                continue;
            }

            try {
                // Find member
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

                // Find or create region (case-insensitive, cached)
                $regionId = $this->resolveRegion($regionName);

                $member->update(['region_id' => $regionId]);
                $this->updated[] = "{$member->full_name} ← {$regionName}";

            } catch (\Throwable $e) {
                $this->errors[] = "الصف {$rowNum}: {$e->getMessage()}";
            }
        }

        $this->rowOffset += $rows->count();
    }

    private function resolveRegion(string $name): int
    {
        $key = mb_strtolower($name);

        if (isset($this->regionCache[$key])) {
            return $this->regionCache[$key];
        }

        // Try exact match first, then case-insensitive
        $region = Region::whereRaw('LOWER(name) = ?', [$key])->first();

        if (!$region) {
            $region = Region::create(['name' => $name, 'is_active' => true]);
            $this->created[] = $name;
        }

        $this->regionCache[$key] = $region->id;

        return $region->id;
    }
}

<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\PaymentInfo;
use App\Models\PaymentInfoAI;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PaymentInfoImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public array $updated = [];
    public array $skipped = [];
    public array $errors  = [];

    private int $rowOffset = 2;

    public function __construct(private string $target = 'payment_info') {}

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $this->rowOffset + $index;

            $dossier       = trim($row['رقم_الملف']        ?? $row['dossier_number']  ?? '');
            $nationalId    = trim($row['رقم_الهوية']       ?? $row['national_id']     ?? '');
            $iban          = str_replace(' ', '', trim($row['الآيبان']        ?? $row['iban']           ?? ''));
            $barcode       = str_replace(' ', '', trim($row['الباركود']       ?? $row['barcode']        ?? ''));
            $recipientName = trim($row['اسم_المستلم']      ?? $row['recipient_name']  ?? $row['اسم المستلم'] ?? '');

            if ($dossier === '' && $nationalId === '') {
                $this->errors[] = "الصف {$rowNum}: لا يوجد رقم ملف أو رقم هوية.";
                continue;
            }

            if ($iban === '' && $barcode === '' && $recipientName === '') {
                $this->skipped[] = "الصف {$rowNum}: لا يوجد آيبان ولا باركود ولا اسم مستلم — تم التخطي.";
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

                if (!$member) {
                    $id = $dossier ?: $nationalId;
                    $this->skipped[] = "الصف {$rowNum}: لم يُعثر على عضو بالمعرّف ({$id}).";
                    continue;
                }

                $fields = array_filter([
                    'iban'           => $iban           ?: null,
                    'barcode'        => $barcode        ?: null,
                    'recipient_name' => $recipientName  ?: null,
                ], fn($v) => $v !== null);

                if ($this->target === 'payment_info_ai') {
                    PaymentInfoAI::updateOrCreate(
                        ['member_id' => $member->id],
                        $fields
                    );
                } else {
                    PaymentInfo::updateOrCreate(
                        ['member_id' => $member->id],
                        $fields
                    );
                }

                $this->updated[] = $member->full_name;
            } catch (\Throwable $e) {
                $this->errors[] = "الصف {$rowNum}: {$e->getMessage()}";
            }
        }

        $this->rowOffset += $rows->count();
    }
}

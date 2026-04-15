<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MatchedPaymentExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    public function query()
    {
        return DB::table('members')
            ->join('payment_info as pi', 'pi.member_id', '=', 'members.id')
            ->join('payment_info_ai as ai', 'ai.member_id', '=', 'members.id')
            ->whereNotNull('pi.iban')->where('pi.iban', '!=', '')
            ->whereNotNull('ai.iban')->where('ai.iban', '!=', '')
            ->whereRaw("REPLACE(pi.iban, ' ', '') = REPLACE(ai.iban, ' ', '')")
            ->select(
                'members.dossier_number',
                'members.full_name',
                DB::raw("REPLACE(pi.iban, ' ', '') as iban"),
                DB::raw("REPLACE(pi.barcode, ' ', '') as barcode"),
            )
            ->orderByRaw('CAST(members.dossier_number AS UNSIGNED) ASC');
    }

    public function title(): string
    {
        return 'المتطابقون';
    }

    public function headings(): array
    {
        return [
            'رقم الملف',
            'الاسم الكامل',
            'الآيبان',
            'الباركود',
        ];
    }

    public function map($row): array
    {
        return [
            $row->dossier_number ?? '',
            $row->full_name      ?? '',
            $row->iban           ?? '',
            $row->barcode        ?? '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF7C3AED']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MatchedAndReviewedExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithCustomValueBinder
{
    private const STRING_COLUMNS = ['D', 'E'];

    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), self::STRING_COLUMNS)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function query()
    {
        return DB::table('members')
            ->join('payment_info as pi', 'pi.member_id', '=', 'members.id')
            ->join('payment_info_ai as ai', 'ai.member_id', '=', 'members.id')
            ->whereNotNull('pi.iban')->where('pi.iban', '!=', '')
            ->whereNotNull('ai.iban')->where('ai.iban', '!=', '')
            ->whereRaw("REPLACE(pi.iban, ' ', '') = REPLACE(ai.iban, ' ', '')")
            ->whereNotNull('members.final_status_id')
            ->select(
                'members.dossier_number',
                'members.full_name',
                'pi.recipient_name',
                DB::raw("REPLACE(pi.iban, ' ', '') as iban"),
                DB::raw("COALESCE(REPLACE(pi.barcode, ' ', ''), '') as barcode"),
            )
            ->orderByRaw('CAST(members.dossier_number AS UNSIGNED) ASC');
    }

    public function title(): string
    {
        return 'متطابقون ومراجَعون';
    }

    public function headings(): array
    {
        return [
            'رقم الملف',
            'الاسم الكامل',
            'اسم المستلم',
            'الآيبان',
            'الباركود',
        ];
    }

    public function map($row): array
    {
        return [
            $row->dossier_number  ?? '',
            $row->full_name       ?? '',
            $row->recipient_name  ?? '',
            $row->iban            ?? '',
            $row->barcode         ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

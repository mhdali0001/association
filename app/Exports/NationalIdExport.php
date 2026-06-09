<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NationalIdExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle, WithCustomValueBinder, WithEvents
{
    public function __construct(protected Builder $query, protected string $sheetTitle = 'أرقام الهوية') {}

    public function bindValue(Cell $cell, $value): bool
    {
        // Force string for dossier and national_id columns to avoid numeric conversion
        if (in_array($cell->getColumn(), ['A', 'D', 'E'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function query(): Builder
    {
        return $this->query->with('region')->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC');
    }

    public function headings(): array
    {
        return [
            'رقم الملف',
            'الاسم الكامل',
            'المنطقة',
            'رقم الهوية الحالي',
            'رقم الهوية الجديد (أدخل هنا)',
        ];
    }

    public function map($member): array
    {
        return [
            $member->dossier_number,
            $member->full_name,
            $member->region?->name ?? '',
            $member->national_id ?? '',
            '',
        ];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0891B2']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                $sheet->setRightToLeft(true);

                // Highlight the "fill here" column in yellow
                if ($lastRow > 1) {
                    $sheet->getDelegate()->getStyle("E2:E{$lastRow}")->applyFromArray([
                        'fill' => [
                            'fillType'   => 'solid',
                            'startColor' => ['rgb' => 'FEFCE8'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'dashed',
                                'color'       => ['rgb' => 'FCD34D'],
                            ],
                        ],
                    ]);
                }

                // Freeze header row
                $sheet->getDelegate()->freezePane('A2');

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(28);
            },
        ];
    }
}

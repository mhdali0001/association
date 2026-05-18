<?php

namespace App\Exports;

use App\Models\Sector;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SectorSheetExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting, WithCustomValueBinder
{
    public function __construct(protected Sector $sector) {}

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'C' && $cell->getRow() > 1) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        return $this->sector->members->filter(fn($m) => $m->estimated_amount > 0);
    }

    public function title(): string
    {
        return mb_substr($this->sector->name, 0, 31);
    }

    public function headings(): array
    {
        return ['رقم الملف', 'الاسم الكامل', 'الإيبان', 'المبلغ النهائي (ل.س)'];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => '#,##0',
        ];
    }

    public function map($member): array
    {
        return [
            $member->dossier_number ?? '',
            $member->full_name,
            $member->paymentInfo?->iban ?? '',
            (int) ($member->estimated_amount ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->setRightToLeft(true);

                if ($lastRow > 1) {
                    // Center dossier column
                    $sheet->getStyle("A2:A{$lastRow}")
                          ->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Zebra rows
                    for ($r = 2; $r <= $lastRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:D{$r}")
                                  ->getFill()
                                  ->setFillType(Fill::FILL_SOLID)
                                  ->getStartColor()->setARGB('FFEFEDFD');
                        }
                    }
                }

                // Total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("C{$totalRow}", 'الإجمالي');
                $sheet->setCellValue("D{$totalRow}", "=SUM(D2:D{$lastRow})");
                $sheet->getStyle("C{$totalRow}:D{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDDD6FE']],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
            },
        ];
    }
}

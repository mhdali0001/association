<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ScoreManagerExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct(protected Builder $query, protected array $excluded = []) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function title(): string
    {
        return 'النقاط';
    }

    public function headings(): array
    {
        return [
            'رقم الاضبارة',
            'الاسم الكامل',
            'العمل',
            'السكن',
            'المعالون',
            'الإعالة',
            'المرض',
            'الحالات الخاصة',
            'إضافة نقاط',
            'سبب الإضافة',
            'انقاص نقاط',
            'سبب الانقاص',
            'الإجمالي',
            'المبلغ (ل.س)',
            'مبلغ الجولة الميدانية (ل.س)',
            'الإجمالي الكلي (ل.س)',
        ];
    }

    public function map($row): array
    {
        $ws  = (int)($row->work_score             ?? 0);
        $hs  = (int)($row->housing_score          ?? 0);
        $ds  = (int)($row->dependents_score       ?? 0);
        $dss = (int)($row->dependent_status_score ?? 0);
        $is  = (int)($row->illness_score          ?? 0);
        $ss  = (int)($row->special_cases_score    ?? 0);
        $add = (int)($row->score_addition         ?? 0);
        $ded = (int)($row->score_deduction        ?? 0);
        $fv  = (float)($row->field_visit_amount   ?? 0);

        $ex = $this->excluded;

        $tot = max(0,
            ($ex['ws']  ?? false ? 0 : $ws)  +
            ($ex['hs']  ?? false ? 0 : $hs)  +
            ($ex['ds']  ?? false ? 0 : $ds)  +
            ($ex['dss'] ?? false ? 0 : $dss) +
            ($ex['is']  ?? false ? 0 : $is)  +
            ($ex['ss']  ?? false ? 0 : $ss)  +
            ($ex['add'] ?? false ? 0 : $add) -
            $ded
        );

        $scoreAmt = $tot * 500;
        $fvAmt    = ($ex['fv'] ?? false) ? 0 : $fv;
        $grandAmt = $scoreAmt + $fvAmt;

        return [
            $row->dossier_number         ?? '',
            $row->full_name              ?? '',
            $ex['ws']  ?? false ? '' : $ws,
            $ex['hs']  ?? false ? '' : $hs,
            $ex['ds']  ?? false ? '' : $ds,
            $ex['dss'] ?? false ? '' : $dss,
            $ex['is']  ?? false ? '' : $is,
            $ex['ss']  ?? false ? '' : $ss,
            $ex['add'] ?? false ? '' : $add,
            $row->score_addition_reason  ?? '',
            (int)($row->score_deduction  ?? 0),
            $row->score_deduction_reason ?? '',
            $tot,
            $scoreAmt,
            $fvAmt ?: '',
            $grandAmt ?: '',
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                $lastRow = $sheet->getHighestRow();

                // Number format for amount columns (D=المبلغ, O=مبلغ الجولة, P=الإجمالي)
                foreach (['N', 'O', 'P'] as $col) {
                    $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                          ->getNumberFormat()
                          ->setFormatCode('#,##0');
                }

                // Center score columns (C–M)
                $sheet->getStyle("C2:M{$lastRow}")
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Zebra rows
                for ($r = 2; $r <= $lastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:P{$r}")
                              ->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setARGB('FFF5F3FF');
                    }
                }
            },
        ];
    }
}

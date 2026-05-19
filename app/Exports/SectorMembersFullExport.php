<?php

namespace App\Exports;

use App\Models\Sector;
use Maatwebsite\Excel\Concerns\Exportable;
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

class SectorMembersFullExport extends DefaultValueBinder implements
    FromCollection, WithHeadings, WithMapping, WithTitle,
    ShouldAutoSize, WithStyles, WithEvents, WithColumnFormatting, WithCustomValueBinder
{
    use Exportable;

    // IBAN column letter (O)
    private const IBAN_COL = 'O';

    public function __construct(
        protected Sector $sector,
        protected \Illuminate\Support\Collection $members,
    ) {}

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === self::IBAN_COL && $cell->getRow() > 1) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->members;
    }

    public function title(): string
    {
        return mb_substr($this->sector->name, 0, 31);
    }

    public function headings(): array
    {
        return [
            'رقم الملف',
            'الاسم الكامل',
            'العمر',
            'الجنس',
            'اسم الأم',
            'رقم الهوية',
            'الهاتف',
            'الهاتف 2',
            'الحالة الاجتماعية',
            'عدد المعالين',
            'العنوان الحالي',
            'المنطقة',
            'حالة التحقق',
            'الحالة النهائية',
            'الإيبان',
            'المبلغ المقدر (ل.س)',
            'عدد الدفعات',
            'الوضع السكني',
            'الجمعية',
            'المفوض',
            'الشخص الثاني',
            'الشبكة',
            'شام كاش',
            'حالات خاصة',
            'وصف الحالة الخاصة',
            'الوظيفة',
            'ملاحظات',
        ];
    }

    public function map($member): array
    {
        return [
            $member->dossier_number ?? '',
            $member->full_name ?? '',
            $member->age ?? '',
            $member->gender ?? '',
            $member->mother_name ?? '',
            $member->national_id ?? '',
            $member->phone ?? '',
            $member->phone2 ?? '',
            $member->marital_status ?? '',
            $member->dependents_count ?? '',
            $member->current_address ?? '',
            $member->region?->name ?? '',
            $member->verificationStatus?->name ?? '',
            $member->finalStatus?->name ?? '',
            $member->paymentInfo?->iban ?? '',
            (int) ($member->estimated_amount ?? 0),
            $member->payments_count ?? '',
            $member->housingStatus?->name ?? '',
            $member->association?->name ?? '',
            $member->delegate ?? '',
            $member->second_person ?? '',
            $member->network ?? '',
            $member->sham_cash_account ?? '',
            $member->special_cases ? 'نعم' : '',
            $member->special_cases_description ?? '',
            $member->job ?? '',
            $member->notes ?? '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            self::IBAN_COL => NumberFormat::FORMAT_TEXT,
            'P'            => '#,##0',
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
                $lastCol = $sheet->getHighestColumn();

                $sheet->setRightToLeft(true);

                if ($lastRow > 1) {
                    // Center dossier column
                    $sheet->getStyle("A2:A{$lastRow}")
                          ->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Zebra rows
                    for ($r = 2; $r <= $lastRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:{$lastCol}{$r}")
                                  ->getFill()
                                  ->setFillType(Fill::FILL_SOLID)
                                  ->getStartColor()->setARGB('FFEFEDFD');
                        }
                    }
                }
            },
        ];
    }
}

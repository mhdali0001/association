<?php

namespace App\Exports;

use App\Models\Note;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NotesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct(protected Collection $notes) {}

    public function collection(): Collection
    {
        return $this->notes;
    }

    public function title(): string
    {
        return 'الملاحظات';
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'التصنيف',
            'العنوان',
            'نص الملاحظة',
            'المستفيدون',
            'عدد المرفقات',
            'الكاتب',
            'مثبّتة',
            'أُنشئت في',
        ];
    }

    /** @param  Note  $note */
    public function map($note): array
    {
        return [
            $note->exactDate(),
            $note->categoryLabel(),
            $note->title ?: '—',
            $note->body,
            $note->members->pluck('full_name')->implode('، ') ?: '—',
            (int) ($note->attachments_count ?? $note->attachments()->count()),
            $note->creator?->name ?? '—',
            $note->pinned ? 'نعم' : 'لا',
            $note->created_at?->format('Y/m/d H:i'),
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:I{$lastRow}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(60);
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(35);

                for ($r = 2; $r <= $lastRow; $r++) {
                    if ($r % 2 === 0) {
                        $sheet->getStyle("A{$r}:I{$r}")->getFill()
                              ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFECFDF5');
                    }
                }
            },
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\PaymentBatch;
use App\Models\PaymentBatchMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PaymentBatchExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    public function __construct(protected PaymentBatch $batch) {}

    public function collection()
    {
        return PaymentBatchMember::with(['member.region', 'member.representative', 'member'])
            ->where('batch_id', $this->batch->id)
            ->leftJoin('members', 'members.id', '=', 'payment_batch_members.member_id')
            ->select('payment_batch_members.*')
            ->orderByRaw('CAST(members.dossier_number AS UNSIGNED)')
            ->get();
    }

    public function title(): string
    {
        return $this->batch->label ?: 'دفعة #' . $this->batch->id;
    }

    public function headings(): array
    {
        return [
            'رقم الملف',
            'الاسم الكامل',
            'رقم الهوية',
            'الحالة الاجتماعية',
            'المنطقة',
            'المبلغ النهائي (ل.س)',
            'رقم الهاتف',
            'المندوب',
        ];
    }

    public function map($row): array
    {
        $member = $row->member;

        return [
            $member?->dossier_number            ?? '',
            $member?->full_name                 ?? 'محذوف',
            $member?->national_id               ?? '',
            $member?->marital_status            ?? '',
            $member?->region?->name             ?? '',
            $row->estimated_amount              ?? '',
            $member?->phone                     ?? '',
            $member?->delegate                  ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D9488']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

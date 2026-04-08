<?php

namespace App\Exports;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class MembersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query(): Builder
    {
        return $this->query->with(['verificationStatus', 'finalStatus', 'association']);
    }

    public function title(): string
    {
        return 'الأعضاء';
    }

    public function headings(): array
    {
        return [
            'رقم الاضبارة',
            'الاسم الكامل',
            'رقم الهوية',
            'العمر',
            'الجنس',
            'اسم الأم',
            'الهاتف',
            'نوع الشبكة',
            'العنوان الحالي',
            'الحالة الاجتماعية',
            'عدد المعالين',
            'حالة السكن',
            'العمل',
            'نوع المرض',
            'حالة خاصة',
            'وصف الحالة الخاصة',
            'شام كاش',
            'حالة التحقق',
            'الحالة النهائية',
            'الجمعية',
            'المندوب',
            'المبلغ المقدر',
            'تاريخ الإضافة',
        ];
    }

    public function map($member): array
    {
        return [
            $member->dossier_number ?? '',
            $member->full_name      ?? '',
            $member->national_id    ?? '',
            $member->age            ?? '',
            $member->gender         ?? '',
            $member->mother_name    ?? '',
            $member->phone          ?? '',
            $member->network        ?? '',
            $member->current_address ?? '',
            $member->marital_status  ?? '',
            $member->dependents_count ?? '',
            $member->housing_status   ?? '',
            $member->job              ?? '',
            $member->disease_type     ?? '',
            $member->special_cases    ? 'نعم' : 'لا',
            $member->special_cases_description ?? '',
            match($member->sham_cash_account) { 'done' => 'تم', 'manual' => 'يدوي', default => 'لا' },
            $member->verificationStatus?->name ?? '',
            $member->finalStatus?->name        ?? '',
            $member->association?->name        ?? '',
            $member->delegate                  ?? '',
            $member->estimated_amount          ?? '',
            $member->created_at?->format('Y-m-d') ?? '',
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

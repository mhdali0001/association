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
        return $this->query->with([
            'verificationStatus', 'finalStatus', 'association',
            'housingStatus', 'region', 'paymentInfo', 'paymentReview',
            'fieldVisits.status', 'fieldVisits.houseType', 'fieldVisits.houseCondition',
        ]);
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
            'الشخص الثاني',
            'الهاتف',
            'الهاتف 2',
            'نوع الشبكة',
            'المنطقة',
            'العنوان الحالي',
            'الحالة الاجتماعية',
            'عدد المعالين',
            'حالة السكن',
            'العمل',
            'حالة المزود',
            'جمعية أخرى',
            'نوع المرض',
            'تفاصيل المرض',
            'حالة خاصة',
            'وصف الحالة الخاصة',
            'النقاط',
            'شام كاش',
            'حالة التحقق',
            'الحالة النهائية',
            'الجمعية',
            'المندوب',
            'المبلغ المقدر',
            'المبلغ النهائي',
            'الآيبان',
            'الباركود',
            'اسم المستلم',
            'حالة مراجعة الدفع',
            'عدد الجولات الميدانية',
            'تاريخ آخر جولة',
            'زائر آخر جولة',
            'حالة آخر جولة',
            'نوع المنزل',
            'حالة المنزل',
            'المبلغ المقدر (الجولة)',
            'سبب المبلغ',
            'ملاحظات الجولة',
            'يوجد فيديو',
            'حالة خاصة (الجولة)',
            'تاريخ الإضافة',
        ];
    }

    public function map($member): array
    {
        $reviewStatus = match($member->paymentReview?->status) {
            'match'    => 'تم',
            'mismatch' => 'غير متطابق',
            'pending'  => 'قيد المراجعة',
            default    => '',
        };

        $lastVisit = $member->fieldVisits->first();

        return [
            $member->dossier_number             ?? '',
            $member->full_name                  ?? '',
            $member->national_id                ?? '',
            $member->age                        ?? '',
            $member->gender                     ?? '',
            $member->mother_name                ?? '',
            $member->second_person              ?? '',
            $member->phone                      ?? '',
            $member->phone2                     ?? '',
            $member->network                    ?? '',
            $member->region?->name              ?? '',
            $member->current_address            ?? '',
            $member->marital_status             ?? '',
            $member->dependents_count           ?? '',
            $member->housingStatus?->name       ?? '',
            $member->job                        ?? '',
            $member->provider_status            ?? '',
            $member->other_association          ? 'نعم' : 'لا',
            $member->disease_type               ?? '',
            $member->illness_details            ?? '',
            $member->special_cases              ? 'نعم' : 'لا',
            $member->special_cases_description  ?? '',
            $member->score                      ?? '',
            match($member->sham_cash_account) { 'done' => 'تم', 'manual' => 'يدوي', default => 'لا' },
            $member->verificationStatus?->name  ?? '',
            $member->finalStatus?->name         ?? '',
            $member->association?->name         ?? '',
            $member->delegate                   ?? '',
            $member->estimated_amount           ?? '',
            $member->final_amount               ?? '',
            $member->paymentInfo?->iban         ?? '',
            $member->paymentInfo?->barcode      ?? '',
            $member->paymentInfo?->recipient_name ?? '',
            $reviewStatus,
            $member->fieldVisits->count(),
            $lastVisit?->visit_date?->format('Y-m-d')      ?? '',
            $lastVisit?->visitor                           ?? '',
            $lastVisit?->status?->name                    ?? '',
            $lastVisit?->houseType?->name                 ?? '',
            $lastVisit?->houseCondition?->name            ?? '',
            $lastVisit?->estimated_amount                 ?? '',
            $lastVisit?->amount_reason                    ?? '',
            $lastVisit?->notes                            ?? '',
            $lastVisit?->has_video    ? 'نعم' : ($lastVisit ? 'لا' : ''),
            $lastVisit?->has_special_case ? 'نعم' : ($lastVisit ? 'لا' : ''),
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

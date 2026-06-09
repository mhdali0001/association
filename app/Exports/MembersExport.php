<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MembersExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithCustomValueBinder, WithEvents
{
    protected Builder $query;

    // Columns that must always be written as strings
    private const STRING_COLUMNS = ['AH', 'AI', 'BK', 'BL'];

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), self::STRING_COLUMNS)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function query(): Builder
    {
        return $this->query->with([
            'verificationStatus', 'finalStatus', 'association',
            'housingStatus', 'region', 'sector', 'paymentInfo', 'paymentInfoAI', 'paymentReview',
            'scores',
            'fieldVisits' => fn($q) => $q->latest()->with(['status', 'houseType', 'houseCondition']),
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
            'اسم المدخل',
            'المبلغ المقدر',
            'المبلغ النهائي',
            'عدد الدفعات',
            'ملاحظة',
            'الآيبان',
            'الباركود',
            'اسم المستلم',
            'اسم مدخل الدفع',
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
            // New columns
            'القطاع',
            'نقاط العمل',
            'نقاط السكن',
            'نقاط المعالين',
            'نقاط الإعالة',
            'نقاط المرض',
            'نقاط الحالات الخاصة',
            'إضافة نقاط',
            'سبب الإضافة',
            'انقاص نقاط',
            'سبب الانقاص',
            'إجمالي النقاط',
            'آيبان (AI)',
            'باركود (AI)',
            'اسم المستلم (AI)',
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

        $lastVisit   = $member->fieldVisits->first();
        $totalAmount = (($member->estimated_amount ?? 0) + ($lastVisit?->estimated_amount ?? 0)) ?: '';

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
            $member->data_entry_name            ?? '',
            $member->estimated_amount           ?? '',
            $totalAmount,
            $member->payments_count             ?? '',
            $member->notes                      ?? '',
            $member->paymentInfo?->iban         ?? '',
            $member->paymentInfo?->barcode      ?? '',
            $member->paymentInfo?->recipient_name   ?? '',
            $member->paymentInfo?->data_entry_name ?? '',
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
            // New columns
            $member->sector?->name                          ?? '',
            $member->scores?->work_score                    ?? '',
            $member->scores?->housing_score                 ?? '',
            $member->scores?->dependents_score              ?? '',
            $member->scores?->dependent_status_score        ?? '',
            $member->scores?->illness_score                 ?? '',
            $member->scores?->special_cases_score           ?? '',
            $member->scores?->score_addition                ?? '',
            $member->scores?->score_addition_reason         ?? '',
            $member->scores?->score_deduction               ?? '',
            $member->scores?->score_deduction_reason        ?? '',
            $member->scores?->total_score                   ?? '',
            $member->paymentInfoAI?->iban                   ?? '',
            $member->paymentInfoAI?->barcode                ?? '',
            $member->paymentInfoAI?->recipient_name         ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $sheet->getHighestRow();
                if ($lastRow < 2) return;
                foreach (self::STRING_COLUMNS as $col) {
                    for ($row = 2; $row <= $lastRow; $row++) {
                        $cell = $sheet->getCell("{$col}{$row}");
                        $cell->getIgnoredErrors()->setNumberStoredAsText(true);
                    }
                }
                $sheet->setRightToLeft(true);
            },
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

<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomMembersExport extends DefaultValueBinder implements
    FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomValueBinder, WithEvents
{
    protected Builder $query;
    protected array   $columns;

    private static array $STRING_KEYS = ['national_id', 'iban', 'barcode', 'iban_ai', 'barcode_ai'];

    // ── Column registry ──────────────────────────────────────────────────
    public static function groups(): array
    {
        return [
            'بيانات أساسية' => [
                'dossier_number'            => 'رقم الاضبارة',
                'full_name'                 => 'الاسم الكامل',
                'national_id'               => 'رقم الهوية',
                'age'                       => 'العمر',
                'gender'                    => 'الجنس',
                'mother_name'               => 'اسم الأم',
                'second_person'             => 'الشخص الثاني',
                'created_at'                => 'تاريخ الإضافة',
            ],
            'معلومات التواصل' => [
                'phone'                     => 'الهاتف',
                'phone2'                    => 'الهاتف 2',
                'network'                   => 'نوع الشبكة',
                'current_address'           => 'العنوان الحالي',
            ],
            'الموقع الجغرافي' => [
                'region'                    => 'المنطقة',
                'sector'                    => 'القطاع',
            ],
            'المعلومات الاجتماعية' => [
                'marital_status'            => 'الحالة الاجتماعية',
                'dependents_count'          => 'عدد المعالين',
                'housing_status'            => 'حالة السكن',
                'job'                       => 'العمل',
                'provider_status'           => 'حالة المزود',
                'other_association'         => 'جمعية أخرى',
            ],
            'الحالة الصحية' => [
                'disease_type'              => 'نوع المرض',
                'illness_details'           => 'تفاصيل المرض',
            ],
            'الحالات الخاصة' => [
                'special_cases'             => 'حالة خاصة',
                'special_cases_description' => 'وصف الحالة الخاصة',
            ],
            'الحالة والتصنيف' => [
                'verification_status'       => 'حالة التحقق',
                'final_status'              => 'الحالة النهائية',
                'association'               => 'الجمعية',
                'sham_cash'                 => 'شام كاش',
            ],
            'النقاط والتقييم' => [
                'score'                     => 'النقاط',
                'work_score'                => 'نقاط العمل',
                'housing_score'             => 'نقاط السكن',
                'dependents_score'          => 'نقاط المعالين',
                'dependent_status_score'    => 'نقاط الإعالة',
                'illness_score'             => 'نقاط المرض',
                'special_cases_score'       => 'نقاط الحالات الخاصة',
                'score_addition'            => 'إضافة نقاط',
                'score_addition_reason'     => 'سبب الإضافة',
                'score_deduction'           => 'انقاص نقاط',
                'score_deduction_reason'    => 'سبب الانقاص',
                'total_score'               => 'إجمالي النقاط',
            ],
            'المعلومات المالية' => [
                'estimated_amount'          => 'المبلغ المقدر',
                'payments_count'            => 'عدد الدفعات',
                'iban'                      => 'الآيبان',
                'barcode'                   => 'الباركود',
                'recipient_name'            => 'اسم المستلم',
                'payment_data_entry'        => 'اسم مدخل الدفع',
                'payment_review_status'     => 'حالة مراجعة الدفع',
                'iban_ai'                   => 'آيبان (AI)',
                'barcode_ai'                => 'باركود (AI)',
                'recipient_name_ai'         => 'اسم المستلم (AI)',
            ],
            'معلومات إدارية' => [
                'delegate'                  => 'المندوب',
                'representative'            => 'المندوب المسؤول',
                'data_entry_name'           => 'اسم المدخل',
                'notes'                     => 'ملاحظة',
            ],
            'الجولة الميدانية' => [
                'field_visits_count'         => 'عدد الجولات',
                'last_visit_date'            => 'تاريخ آخر جولة',
                'last_visit_visitor'         => 'زائر آخر جولة',
                'last_visit_status'          => 'حالة آخر جولة',
                'last_visit_house_type'      => 'نوع المنزل',
                'last_visit_house_condition' => 'حالة المنزل',
                'last_visit_amount'          => 'المبلغ المقدر (الجولة)',
                'last_visit_amount_reason'   => 'سبب المبلغ',
                'last_visit_notes'           => 'ملاحظات الجولة',
                'last_visit_has_video'       => 'يوجد فيديو',
                'last_visit_has_special_case'=> 'حالة خاصة (الجولة)',
            ],
        ];
    }

    public static function allColumns(): array
    {
        return array_merge(...array_values(self::groups()));
    }

    // ── Constructor ───────────────────────────────────────────────────────
    public function __construct(Builder $query, array $columns)
    {
        $this->query   = $query;
        $this->columns = $columns;
    }

    // ── Value binder: force string for sensitive columns ──────────────────
    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), $this->stringColumnLetters())) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    private function stringColumnLetters(): array
    {
        $letters = [];
        foreach ($this->columns as $i => $key) {
            if (in_array($key, self::$STRING_KEYS)) {
                $letters[] = Coordinate::stringFromColumnIndex($i + 1);
            }
        }
        return $letters;
    }

    // ── Query ─────────────────────────────────────────────────────────────
    public function query(): Builder
    {
        $with = ['region', 'sector', 'verificationStatus', 'finalStatus', 'association', 'housingStatus'];

        $scoreKeys   = ['score', 'work_score', 'housing_score', 'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score', 'score_addition', 'score_addition_reason', 'score_deduction', 'score_deduction_reason', 'total_score'];
        $paymentKeys = ['iban', 'barcode', 'recipient_name', 'payment_data_entry'];
        $aiKeys      = ['iban_ai', 'barcode_ai', 'recipient_name_ai'];
        $visitKeys   = ['field_visits_count', 'last_visit_date', 'last_visit_visitor', 'last_visit_status', 'last_visit_house_type', 'last_visit_house_condition', 'last_visit_amount', 'last_visit_amount_reason', 'last_visit_notes', 'last_visit_has_video', 'last_visit_has_special_case'];

        if (array_intersect($this->columns, $scoreKeys))   $with[] = 'scores';
        if (array_intersect($this->columns, $paymentKeys)) $with[] = 'paymentInfo';
        if (array_intersect($this->columns, $aiKeys))      $with[] = 'paymentInfoAI';
        if (in_array('payment_review_status', $this->columns)) $with[] = 'paymentReview';
        if (in_array('representative', $this->columns))        $with[] = 'representative';
        if (array_intersect($this->columns, $visitKeys)) {
            $with[] = ['fieldVisits' => fn($q) => $q->latest()->with(['status', 'houseType', 'houseCondition'])];
        }

        return $this->query->with($with);
    }

    // ── Headings ──────────────────────────────────────────────────────────
    public function headings(): array
    {
        $all = self::allColumns();
        return array_values(array_map(fn($k) => $all[$k] ?? $k, $this->columns));
    }

    // ── Row mapping ───────────────────────────────────────────────────────
    public function map($m): array
    {
        $lastVisit = $m->fieldVisits?->first();

        $row = [];
        foreach ($this->columns as $col) {
            $row[] = match ($col) {
                'dossier_number'            => $m->dossier_number ?? '',
                'full_name'                 => $m->full_name ?? '',
                'national_id'               => $m->national_id ?? '',
                'age'                       => $m->age ?? '',
                'gender'                    => $m->gender ?? '',
                'mother_name'               => $m->mother_name ?? '',
                'second_person'             => $m->second_person ?? '',
                'created_at'                => $m->created_at?->format('Y-m-d') ?? '',
                'phone'                     => $m->phone ?? '',
                'phone2'                    => $m->phone2 ?? '',
                'network'                   => $m->network ?? '',
                'current_address'           => $m->current_address ?? '',
                'region'                    => $m->region?->name ?? '',
                'sector'                    => $m->sector?->name ?? '',
                'marital_status'            => $m->marital_status ?? '',
                'dependents_count'          => $m->dependents_count ?? '',
                'housing_status'            => $m->housingStatus?->name ?? '',
                'job'                       => $m->job ?? '',
                'provider_status'           => $m->provider_status ?? '',
                'other_association'         => $m->other_association ? 'نعم' : 'لا',
                'disease_type'              => $m->disease_type ?? '',
                'illness_details'           => $m->illness_details ?? '',
                'special_cases'             => $m->special_cases ? 'نعم' : 'لا',
                'special_cases_description' => $m->special_cases_description ?? '',
                'verification_status'       => $m->verificationStatus?->name ?? '',
                'final_status'              => $m->finalStatus?->name ?? '',
                'association'               => $m->association?->name ?? '',
                'sham_cash'                 => match ($m->sham_cash_account) { 'done' => 'تم', 'manual' => 'يدوي', default => 'لا' },
                'score'                     => $m->score ?? '',
                'work_score'                => $m->scores?->work_score ?? '',
                'housing_score'             => $m->scores?->housing_score ?? '',
                'dependents_score'          => $m->scores?->dependents_score ?? '',
                'dependent_status_score'    => $m->scores?->dependent_status_score ?? '',
                'illness_score'             => $m->scores?->illness_score ?? '',
                'special_cases_score'       => $m->scores?->special_cases_score ?? '',
                'score_addition'            => $m->scores?->score_addition ?? '',
                'score_addition_reason'     => $m->scores?->score_addition_reason ?? '',
                'score_deduction'           => $m->scores?->score_deduction ?? '',
                'score_deduction_reason'    => $m->scores?->score_deduction_reason ?? '',
                'total_score'               => $m->scores?->total_score ?? '',
                'estimated_amount'          => $m->estimated_amount ?? '',
                'payments_count'            => $m->payments_count ?? '',
                'iban'                      => $m->paymentInfo?->iban ?? '',
                'barcode'                   => $m->paymentInfo?->barcode ?? '',
                'recipient_name'            => $m->paymentInfo?->recipient_name ?? '',
                'payment_data_entry'        => $m->paymentInfo?->data_entry_name ?? '',
                'payment_review_status'     => match ($m->paymentReview?->status) { 'match' => 'تم', 'mismatch' => 'غير متطابق', 'pending' => 'قيد المراجعة', default => '' },
                'iban_ai'                   => $m->paymentInfoAI?->iban ?? '',
                'barcode_ai'               => $m->paymentInfoAI?->barcode ?? '',
                'recipient_name_ai'         => $m->paymentInfoAI?->recipient_name ?? '',
                'delegate'                  => $m->delegate ?? '',
                'representative'            => $m->representative?->name ?? '',
                'data_entry_name'           => $m->data_entry_name ?? '',
                'notes'                     => $m->notes ?? '',
                'field_visits_count'         => $m->fieldVisits?->count() ?? 0,
                'last_visit_date'            => $lastVisit?->visit_date?->format('Y-m-d') ?? '',
                'last_visit_visitor'         => $lastVisit?->visitor ?? '',
                'last_visit_status'          => $lastVisit?->status?->name ?? '',
                'last_visit_house_type'      => $lastVisit?->houseType?->name ?? '',
                'last_visit_house_condition' => $lastVisit?->houseCondition?->name ?? '',
                'last_visit_amount'          => $lastVisit?->estimated_amount ?? '',
                'last_visit_amount_reason'   => $lastVisit?->amount_reason ?? '',
                'last_visit_notes'           => $lastVisit?->notes ?? '',
                'last_visit_has_video'       => $lastVisit ? ($lastVisit->has_video ? 'نعم' : 'لا') : '',
                'last_visit_has_special_case'=> $lastVisit ? ($lastVisit->has_special_case ? 'نعم' : 'لا') : '',
                default                      => '',
            };
        }
        return $row;
    }

    // ── Styles ────────────────────────────────────────────────────────────
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

    // ── Events ────────────────────────────────────────────────────────────
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $sheet->setRightToLeft(true);

                foreach ($this->stringColumnLetters() as $letter) {
                    for ($row = 2; $row <= $lastRow; $row++) {
                        $sheet->getCell("{$letter}{$row}")->getIgnoredErrors()->setNumberStoredAsText(true);
                    }
                }
            },
        ];
    }
}

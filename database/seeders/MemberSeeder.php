<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'full_name'                 => 'فاطمة أحمد الخطيب',
                'age'                       => 45,
                'gender'                    => 'أنثى',
                'mother_name'               => 'مريم سالم',
                'national_id'               => '10234567891',
                'verification_status_id'    => 1,
                'dossier_number'            => '2026/001',
                'current_address'           => 'دمشق - مخيم اليرموك، شارع فلسطين بناء 14',
                'marital_status'            => 'أرملة',
                'disease_type'              => 'ضغط الدم - سكري',
                'other_association'         => false,
                'phone'                     => '0933112233',
                'representative_id'         => 2,
                'network'                   => 'MTN',
                'provider_status'           => 'نشط',
                'job'                       => 'ربة منزل',
                'housing_status'            => 'إيجار',
                'dependents_count'          => 4,
                'illness_details'           => 'تعاني من ضغط الدم المرتفع والسكري منذ 10 سنوات، تحتاج دواءً شهرياً منتظماً',
                'special_cases'             => false,
                'special_cases_description' => null,
                'sham_cash_account'         => true,
                'score'                     => 14,
                'estimated_amount'          => 7000.00,
                'created_at'               => '2026-03-01 09:00:00',
                'updated_at'               => '2026-03-01 09:00:00',
            ],
            [
                'full_name'                 => 'أم محمد سلمى درويش',
                'age'                       => 52,
                'gender'                    => 'أنثى',
                'mother_name'               => 'خديجة يوسف',
                'national_id'              => '10345678902',
                'verification_status_id'    => 1,
                'dossier_number'            => '2026/002',
                'current_address'           => 'ريف دمشق - دوما، حي الحجر الأسود',
                'marital_status'            => 'مطلقة',
                'disease_type'              => 'أمراض قلب',
                'other_association'         => false,
                'phone'                     => '0944223344',
                'representative_id'         => 2,
                'network'                   => 'SYRIATEL',
                'provider_status'           => 'نشط',
                'job'                       => 'خياطة منزلية',
                'housing_status'            => 'استضافة',
                'dependents_count'          => 3,
                'illness_details'           => 'تعاني من قصور في عضلة القلب وتحتاج متابعة طبية مستمرة وأدوية مرتفعة التكلفة',
                'special_cases'             => true,
                'special_cases_description' => 'ابنها الأكبر معتقل منذ 2022، تعيش وحدها مع أطفال صغار',
                'sham_cash_account'         => false,
                'score'                     => 18,
                'estimated_amount'          => 9000.00,
                'created_at'               => '2026-03-02 10:30:00',
                'updated_at'               => '2026-03-02 10:30:00',
            ],
            [
                'full_name'                 => 'نور الهدى إبراهيم حسن',
                'age'                       => 38,
                'gender'                    => 'أنثى',
                'mother_name'               => 'سعاد محمود',
                'national_id'               => '10456789013',
                'verification_status_id'    => 2,
                'dossier_number'            => '2026/003',
                'current_address'           => 'حلب - الشيخ مقصود، بناء الأمل طابق 3',
                'marital_status'            => 'أرملة',
                'disease_type'              => 'إعاقة حركية جزئية',
                'other_association'         => true,
                'phone'                     => '0955334455',
                'representative_id'         => 3,
                'network'                   => 'MTN',
                'provider_status'           => 'نشط',
                'job'                       => null,
                'housing_status'            => 'ملك',
                'dependents_count'          => 5,
                'illness_details'           => 'فقدت القدرة على المشي بشكل كامل إثر إصابة عام 2020، تستخدم كرسياً متحركاً',
                'special_cases'             => true,
                'special_cases_description' => 'إعاقة دائمة تستدعي رعاية مستمرة، لا يوجد معيل آخر في الأسرة',
                'sham_cash_account'         => true,
                'score'                     => 21,
                'estimated_amount'          => 10500.00,
                'created_at'               => '2026-03-03 08:15:00',
                'updated_at'               => '2026-03-03 08:15:00',
            ],
            [
                'full_name'                 => 'رنا خالد المصطفى',
                'age'                       => 31,
                'gender'                    => 'أنثى',
                'mother_name'               => 'هناء علي',
                'national_id'               => '10567890124',
                'verification_status_id'    => 4,
                'dossier_number'            => '2026/004',
                'current_address'           => 'حمص - باب هود، زقاق النور رقم 7',
                'marital_status'            => 'مطلقة',
                'disease_type'              => null,
                'other_association'         => false,
                'phone'                     => '0966445566',
                'representative_id'         => 3,
                'network'                   => 'SYRIATEL',
                'provider_status'           => 'موقوف مؤقتاً',
                'job'                       => 'معلمة متطوعة',
                'housing_status'            => 'إيجار',
                'dependents_count'          => 2,
                'illness_details'           => null,
                'special_cases'             => false,
                'special_cases_description' => null,
                'sham_cash_account'         => false,
                'score'                     => 8,
                'estimated_amount'          => 4000.00,
                'created_at'               => '2026-03-04 11:00:00',
                'updated_at'               => '2026-03-04 11:00:00',
            ],
            [
                'full_name'                 => 'أم علي زينب الحسن',
                'age'                       => 61,
                'gender'                    => 'أنثى',
                'mother_name'               => 'وفاء ناصر',
                'national_id'               => '10678901235',
                'verification_status_id'    => 1,
                'dossier_number'            => '2026/005',
                'current_address'           => 'اللاذقية - الزراعة، شارع 8 آذار بناء 22',
                'marital_status'            => 'أرملة',
                'disease_type'              => 'سرطان الثدي - مرحلة أولى',
                'other_association'         => false,
                'phone'                     => '0977556677',
                'representative_id'         => 2,
                'network'                   => 'MTN',
                'provider_status'           => 'نشط',
                'job'                       => 'ربة منزل',
                'housing_status'            => 'ملك',
                'dependents_count'          => 6,
                'illness_details'           => 'تتلقى علاجاً كيميائياً في مشفى تشرين، التكاليف مرتفعة جداً وتحتاج دعماً عاجلاً',
                'special_cases'             => true,
                'special_cases_description' => 'حالة طبية طارئة - علاج سرطاني مكلف، أسرة كبيرة بلا معيل',
                'sham_cash_account'         => true,
                'score'                     => 25,
                'estimated_amount'          => 12500.00,
                'created_at'               => '2026-03-05 14:00:00',
                'updated_at'               => '2026-03-05 14:00:00',
            ],
        ];

        foreach ($members as $memberData) {
            $id = DB::table('members')->insertGetId($memberData);

            // نقاط التقييم
            $scoreMap = [
                1 => ['work_score' => 2, 'housing_score' => 2, 'dependents_score' => 8,  'illness_score' => 2, 'special_cases_score' => 0,  'total_score' => 14],
                2 => ['work_score' => 1, 'housing_score' => 1, 'dependents_score' => 6,  'illness_score' => 4, 'special_cases_score' => 6,  'total_score' => 18],
                3 => ['work_score' => 2, 'housing_score' => 2, 'dependents_score' => 10, 'illness_score' => 2, 'special_cases_score' => 5,  'total_score' => 21],
                4 => ['work_score' => 1, 'housing_score' => 2, 'dependents_score' => 4,  'illness_score' => 1, 'special_cases_score' => 0,  'total_score' => 8],
                5 => ['work_score' => 2, 'housing_score' => 2, 'dependents_score' => 11, 'illness_score' => 5, 'special_cases_score' => 5,  'total_score' => 25],
            ];

            $idx = DB::table('members')->where('id', $id)->value('id');
            $pos = array_search($memberData['dossier_number'], array_column($members, 'dossier_number')) + 1;

            DB::table('member_scores')->insert(array_merge(
                ['member_id' => $id, 'created_at' => $memberData['created_at'], 'updated_at' => $memberData['updated_at']],
                $scoreMap[$pos]
            ));

            // معلومات الدفع
            $paymentData = [
                1 => ['iban' => 'SY59006001234567890123456',  'barcode' => 'BC-2026-001-FAT'],
                2 => ['iban' => 'SY59006009876543210987654',  'barcode' => 'BC-2026-002-SAL'],
                3 => ['iban' => null,                          'barcode' => 'BC-2026-003-NOR'],
                4 => ['iban' => 'SY59006001122334455667788',  'barcode' => null],
                5 => ['iban' => 'SY59006005544332211009988',  'barcode' => 'BC-2026-005-ZIN'],
            ];

            DB::table('payment_info')->insert([
                'member_id'     => $id,
                'iban'          => $paymentData[$pos]['iban'],
                'barcode'       => $paymentData[$pos]['barcode'],
                'iban_image'    => null,
                'barcode_image' => null,
                'created_at'    => $memberData['created_at'],
                'updated_at'    => $memberData['updated_at'],
            ]);
        }
    }
}

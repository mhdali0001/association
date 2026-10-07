<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * المناطق الجديدة من ملف "داتا جديدة" مع اسم القطاع (null = بدون قطاع).
     */
    private array $regions = [
        'القطيفة'          => 'القلمون',
        'معضمية القلمون'   => 'القلمون',
        'الشيخ محي الدين'  => 'دمشق جبل قاسيون',
        'الصالحية'         => 'دمشق جبل قاسيون',
        'مديرا'            => 'غوطة شرقية',
        'باب السلام'       => 'وسط دمشق',
        'ماعص'             => null,
        'حمريت'            => null,
        'عين التينة'       => null,
        'التقدم'           => null,
    ];

    private string $maritalStatus = 'ارمل';

    public function up(): void
    {
        $sectorIds = DB::table('sectors')->pluck('id', 'name');
        $now = now();

        foreach ($this->regions as $name => $sectorName) {
            DB::table('regions')->insertOrIgnore([
                'name'       => $name,
                'is_active'  => true,
                'sector_id'  => $sectorName ? ($sectorIds[$sectorName] ?? null) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (!DB::table('marital_statuses')->where('name', $this->maritalStatus)->exists()) {
            DB::table('marital_statuses')->insert(['name' => $this->maritalStatus, 'is_active' => 1]);
        }
    }

    public function down(): void
    {
        // لا نحذف منطقة أو حالة مرتبطة بمستفيدين.
        DB::table('regions')
            ->whereIn('name', array_keys($this->regions))
            ->whereNotExists(fn($q) => $q->from('members')->whereColumn('members.region_id', 'regions.id'))
            ->delete();

        if (!DB::table('members')->where('marital_status', $this->maritalStatus)->exists()) {
            DB::table('marital_statuses')->where('name', $this->maritalStatus)->delete();
        }
    }
};

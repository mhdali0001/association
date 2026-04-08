<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AgeStatisticsController extends Controller
{
    public function index()
    {
        $base = DB::table('members')->whereNotNull('age')->where('age', '>', 0);

        // Core stats
        $stats = (clone $base)->selectRaw('
            COUNT(*)        AS total,
            AVG(age)        AS avg_age,
            MIN(age)        AS min_age,
            MAX(age)        AS max_age
        ')->first();

        $totalWithAge    = (int) $stats->total;
        $totalMembers    = DB::table('members')->count();
        $totalWithoutAge = $totalMembers - $totalWithAge;
        $avgAge          = round($stats->avg_age, 1);
        $minAge          = (int) $stats->min_age;
        $maxAge          = (int) $stats->max_age;

        // Median — get sorted ages and pick middle value
        $ages   = (clone $base)->orderBy('age')->pluck('age')->toArray();
        $count  = count($ages);
        $median = $count > 0
            ? ($count % 2 === 0
                ? round(($ages[$count / 2 - 1] + $ages[$count / 2]) / 2, 1)
                : $ages[intval($count / 2)])
            : 0;

        // Age groups
        $groups = [
            ['label' => 'أقل من 18',   'min' => 0,  'max' => 17,  'color' => '#6366f1'],
            ['label' => '18 – 30',      'min' => 18, 'max' => 30,  'color' => '#3b82f6'],
            ['label' => '31 – 45',      'min' => 31, 'max' => 45,  'color' => '#10b981'],
            ['label' => '46 – 60',      'min' => 46, 'max' => 60,  'color' => '#f59e0b'],
            ['label' => '61 – 75',      'min' => 61, 'max' => 75,  'color' => '#f97316'],
            ['label' => 'أكثر من 75',  'min' => 76, 'max' => 999, 'color' => '#ef4444'],
        ];

        foreach ($groups as &$g) {
            $g['count'] = (clone $base)
                ->whereBetween('age', [$g['min'], $g['max']])
                ->count();
            $g['pct'] = $totalWithAge > 0 ? round($g['count'] / $totalWithAge * 100, 1) : 0;
        }
        unset($g);

        // Most common ages (top 10)
        $topAges = (clone $base)
            ->select('age', DB::raw('COUNT(*) as cnt'))
            ->groupBy('age')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // Age of youngest / oldest members
        $youngest = DB::table('members')->whereNotNull('age')->where('age', '>', 0)->orderBy('age')->first(['full_name', 'age', 'dossier_number']);
        $oldest   = DB::table('members')->whereNotNull('age')->where('age', '>', 0)->orderByDesc('age')->first(['full_name', 'age', 'dossier_number']);

        // Distribution by decade for chart (10-19, 20-29, …)
        $decades = [];
        for ($d = 10; $d <= 90; $d += 10) {
            $cnt = (clone $base)->whereBetween('age', [$d, $d + 9])->count();
            if ($cnt > 0 || ($d >= 20 && $d <= 70)) {
                $decades[] = ['label' => $d . '–' . ($d + 9), 'count' => $cnt];
            }
        }

        return view('age-statistics.index', compact(
            'totalMembers', 'totalWithAge', 'totalWithoutAge',
            'avgAge', 'minAge', 'maxAge', 'median',
            'groups', 'topAges', 'youngest', 'oldest', 'decades'
        ));
    }
}

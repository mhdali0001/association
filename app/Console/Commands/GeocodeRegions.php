<?php

namespace App\Console\Commands;

use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * تحديد موقع تقريبي لكل منطقة لعرض الأعضاء على الخريطة.
 *  1) البحث في OpenStreetMap (خدمة Nominatim) — يُرسل اسم المنطقة فقط.
 *     الأسماء محفوظة بلا "ال" وبالهاء بدل التاء المربوطة (ركن دين، حزه) فتُجرَّب صيغ بديلة،
 *     وتُرفض النتائج البعيدة عن قطاع المنطقة (حي دمشقي طابق بلدة بنفس الاسم في محافظة أخرى).
 *  2) ما لم يُعثر عليه يوضع في مركز قطاعه (وسيط مواقع مناطق القطاع التي عُثر عليها).
 */
class GeocodeRegions extends Command
{
    protected $signature = 'regions:geocode {--all : إعادة تحديد كل المناطق وليس التي بلا موقع فقط}';

    protected $description = 'تحديد موقع تقريبي للمناطق (OpenStreetMap ثم مركز القطاع) لعرض الأعضاء على الخريطة';

    private const DAMASCUS           = [33.5138, 36.2765];
    private const OTHER_GOVERNORATES = 'محافظات أخرى';

    public function handle(): int
    {
        $regions = Region::with('sector')
            ->when(! $this->option('all'), fn ($q) => $q->whereNull('latitude'))
            ->orderBy('name')
            ->get();

        $this->info("مناطق للمعالجة: {$regions->count()} (طلب واحد في الثانية حسب سياسة الخدمة)");
        $missing = [];

        foreach ($regions as $region) {
            if ($hit = $this->lookup($region)) {
                $region->update(['latitude' => $hit['lat'], 'longitude' => $hit['lng']]);
                $this->line(sprintf('  ✔ %s ← "%s" (%s، %d كم من دمشق)', $region->name, $hit['query'], $hit['type'], $hit['km']));
            } else {
                $missing[] = $region;
            }
        }

        // ما لم يُعثر عليه: مركز قطاعه التقريبي (من المناطق التي عُثر عليها فقط)
        $failed     = [];
        $missingIds = array_map(fn (Region $r) => $r->id, $missing);
        foreach ($missing as $region) {
            if ($center = $this->sectorCenter($region, $missingIds)) {
                $region->update(['latitude' => $center[0], 'longitude' => $center[1]]);
                $this->line("  ≈ {$region->name} ← مركز قطاع {$region->sector->name}");
            } else {
                $failed[] = $region->name;
                $this->line("  ✘ {$region->name}");
            }
        }

        $this->newLine();
        $this->info(sprintf('بالبحث: %d، بمركز القطاع: %d، بلا موقع: %d',
            $regions->count() - count($missing), count($missing) - count($failed), count($failed)));
        if ($failed) {
            $this->warn('بلا موقع (لا يظهر أعضاؤها على الخريطة): ' . implode('، ', $failed));
        }

        return self::SUCCESS;
    }

    /** @return array{lat: float, lng: float, query: string, type: string, km: int}|null */
    private function lookup(Region $region): ?array
    {
        $sector     = $region->sector?->name;
        $streetLike = (bool) preg_match('/^(شارع|باب|ساحه|ساحة|حي|كراج)\s/u', $region->name);

        // سوريا أولاً، ثم لبنان (مستفيدون مقيمون هناك) لما لم يُعثر عليه
        foreach (['sy', 'lb'] as $country) {
            foreach ($this->variants($region->name) as $query) {
                // من النتائج المقبولة: الأقرب إلى دمشق (أسماء الشوارع والأحياء تتكرر في مدن كثيرة)
                $best = collect($this->search($query, $country, $streetLike))
                    ->map(fn ($p) => $p + ['km' => (int) round($this->distanceKm(self::DAMASCUS, [$p['lat'], $p['lng']]))])
                    ->filter(fn ($p) => $this->plausible($sector, $p['km'], $p['type']))
                    ->sortBy('km')
                    ->first();

                if ($best) {
                    return $best + ['query' => $query];
                }
            }
        }

        return null;
    }

    /**
     * هل تتفق المسافة عن دمشق مع قطاع المنطقة؟
     */
    private function plausible(?string $sector, int $km, string $type): bool
    {
        return match (true) {
            $sector === self::OTHER_GOVERNORATES                             => true,
            $sector === null                                                 => $km <= 110 || $type === 'city', // مدينة كبرى بعيدة (دير الزور) مقبولة
            str_starts_with($sector, 'دمشق ') || $sector === 'وسط دمشق'      => $km <= 10,  // أحياء داخل مدينة دمشق
            str_contains($sector, 'دمشق')                                    => $km <= 30,  // ضواحي دمشق (جنوب دمشق)
            default                                                          => $km <= 110, // ريف دمشق
        };
    }

    /** @return list<array{lat: float, lng: float, type: string}> */
    private function search(string $query, string $country, bool $streetLike): array
    {
        usleep(1_100_000);

        $response = Http::withHeaders(['User-Agent' => 'masalkalnour-region-geocoder/1.0 (+' . config('app.url') . ')'])
            ->timeout(20)
            ->retry(2, 2000, throw: false)
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query, 'countrycodes' => $country, 'format' => 'jsonv2', 'limit' => 5, 'accept-language' => 'ar',
            ]);

        // أماكن مأهولة أو حدود إدارية؛ والشوارع والأبواب والساحات حين يكون الاسم نفسه "شارع/باب/ساحة..."
        $categories = $streetLike ? ['place', 'boundary', 'highway', 'historic', 'landuse'] : ['place', 'boundary'];

        return collect($response->successful() ? (array) $response->json() : [])
            ->filter(fn ($p) => in_array($p['category'] ?? '', $categories, true))
            ->map(fn ($p) => ['lat' => (float) $p['lat'], 'lng' => (float) $p['lon'], 'type' => $p['type'] ?? ''])
            ->values()
            ->all();
    }

    /**
     * "ركن دين" ← ركن الدين، "حزه" ← حزة، "قابون" ← القابون، "دف شوك" ← دف الشوك،
     * "الشبعه" ← شبعا، "ريف درعا" ← درعا.
     */
    private function variants(string $name): array
    {
        $name = preg_replace('/^ريف\s+/u', '', trim($name)); // "ريف درعا": موقع المحافظة تقريباً

        $words  = preg_split('/\s+/u', $name);
        $taa    = array_map(fn ($w) => preg_replace('/ه$/u', 'ة', $w), $words);
        $alif   = array_map(fn ($w) => preg_replace('/ه$/u', 'ا', $w), $words);
        $noAl   = array_map(fn ($w) => preg_replace('/^ال(?=\p{L}{2})/u', '', $w), $alif);
        $withAl = fn ($w) => (str_starts_with($w, 'ال') || preg_match('/^\d/', $w)) ? $w : 'ال' . $w;
        $restAl = fn (array $ws) => implode(' ', array_merge([$ws[0]], array_map($withAl, array_slice($ws, 1))));

        return array_values(array_unique(array_filter([
            implode(' ', $words),
            implode(' ', $taa),
            count($words) > 1 ? $restAl($taa) : null,
            count($words) > 1 ? $restAl($words) : null,
            implode(' ', array_map($withAl, $taa)),
            implode(' ', array_map($withAl, $words)),
            implode(' ', $noAl),
        ])));
    }

    /**
     * مركز تقريبي لقطاع المنطقة: وسيط مواقع مناطق القطاع التي لها موقع (منطقتان على الأقل).
     */
    private function sectorCenter(Region $region, array $excludeIds): ?array
    {
        if (! $region->sector || $region->sector->name === self::OTHER_GOVERNORATES) {
            return null;
        }

        $points = Region::where('sector_id', $region->sector_id)
            ->whereNotIn('id', $excludeIds)
            ->whereNotNull('latitude')
            ->get(['latitude', 'longitude']);

        if ($points->count() < 2) {
            return null;
        }

        $median = function ($values) {
            $v = $values->map(fn ($x) => (float) $x)->sort()->values();
            $n = $v->count();
            return $n % 2 ? $v[intdiv($n, 2)] : ($v[$n / 2 - 1] + $v[$n / 2]) / 2;
        };

        return [$median($points->pluck('latitude')), $median($points->pluck('longitude'))];
    }

    private function distanceKm(array $a, array $b): float
    {
        [$lat1, $lng1, $lat2, $lng2] = array_map('deg2rad', [$a[0], $a[1], $b[0], $b[1]]);
        $h = sin(($lat2 - $lat1) / 2) ** 2 + cos($lat1) * cos($lat2) * sin(($lng2 - $lng1) / 2) ** 2;

        return 6371 * 2 * asin(sqrt($h));
    }
}

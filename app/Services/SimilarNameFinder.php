<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * كشف الأسماء المتشابهة التي قد تكون لنفس الشخص لكنها كُتبت بشكل خاطئ:
 * - اختلاف الهمزات والتاء المربوطة والألف المقصورة والتشكيل والمسافات (تطابق بعد التوحيد)
 * - خطأ إملائي بحرف أو حرفين (مسافة التحرير Levenshtein على مستوى الحرف)
 * - اسم ناقص/زائد (نفس الاسم الأول والأخير مع جزء أوسط محذوف)
 */
class SimilarNameFinder
{
    public const TYPE_NORMALIZED = 'normalized';
    public const TYPE_SPELLING   = 'spelling';
    public const TYPE_PARTIAL    = 'partial';

    // كلمات تُلصق بما بعدها: "عبد الرزاق" = "عبدالرزاق"
    private const JOIN_NEXT = ['عبد', 'ابو', 'ام', 'بو'];
    // كلمات تُلصق بما قبلها: "نور الدين" = "نورالدين"
    private const JOIN_PREV = ['الدين', 'الله', 'الرحمن'];

    /** توحيد الإملاء العربي للمقارنة */
    public static function normalize(?string $name): string
    {
        $s = (string) $name;
        // التشكيل والتطويل
        $s = preg_replace('/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}\x{0640}]/u', '', $s);
        $s = strtr($s, [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا',
            'ة' => 'ه', 'ى' => 'ي', 'ئ' => 'ي', 'ؤ' => 'و', 'ء' => '',
            'ی' => 'ي', 'ک' => 'ك',
        ]);
        $s = mb_strtolower($s);
        $s = preg_replace('/[^\p{L}\s]+/u', ' ', $s);

        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    /**
     * يقسم الاسم إلى كلمات مع دمج المركّبات (عبد/أبو/الدين/الله).
     * كل عنصر: ['text' => النص الأصلي, 'key' => الصيغة الموحّدة بلا مسافات]
     */
    public static function tokens(?string $name): array
    {
        $raw = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        $out = [];
        foreach ($raw as $word) {
            $key = str_replace(' ', '', self::normalize($word));
            if ($key === '') {
                continue;
            }
            $last = count($out) - 1;
            $joinPrev = $last >= 0 && (in_array($key, self::JOIN_PREV, true) || in_array($out[$last]['key'], self::JOIN_NEXT, true));
            if ($joinPrev) {
                $out[$last]['text'] .= ' ' . $word;
                $out[$last]['key']  .= $key;
            } else {
                $out[] = ['text' => $word, 'key' => $key];
            }
        }
        return $out;
    }

    /**
     * @param  Collection  $members  أعضاء فيهم id و full_name على الأقل
     * تُقارن الكتابات المختلفة للاسم (لا السجلات)، فكل مجموعة = كتابتان مشتبه بأنهما لنفس الشخص،
     * ومعهما كل السجلات التي تحمل أياً منهما. لا نربط المجموعات ببعضها تسلسلياً كي لا يختلط الإخوة.
     *
     * @param  int  $minSimilarity  الحد الأدنى للتشابه (نسبة مئوية) لاعتبار الخطأ الإملائي
     * @return array  ['sides' => [[ids بالكتابة الأولى], [ids بالكتابة الثانية]], 'ids' => [...], 'types' => [type => similarity]]
     */
    public function findGroups(Collection $members, int $minSimilarity = 85, bool $includePartial = true): array
    {
        $items = [];
        foreach ($members as $m) {
            $raw = trim(preg_replace('/\s+/u', ' ', (string) $m->full_name));
            if (isset($items[$raw])) {
                $items[$raw]['ids'][] = $m->id;
                continue;
            }
            $tokens = self::tokens($raw);
            if (! $tokens) {
                continue;
            }
            $keys = array_column($tokens, 'key');
            $items[$raw] = [
                'ids'     => [$m->id],
                'raw'     => $raw,
                'keys'    => $keys,
                'compact' => implode('', $keys),
            ];
        }
        $items = array_values($items);

        // يحوّل كل حرف إلى بايت واحد كي تعمل levenshtein() على مستوى الحرف لا البايت
        $charMap  = [];
        foreach ($items as $i => $it) {
            $encoded = '';
            foreach (mb_str_split($it['compact']) as $ch) {
                $charMap[$ch] ??= chr((count($charMap) % 255) + 1);
                $encoded .= $charMap[$ch];
            }
            $items[$i]['enc'] = $encoded;
            $items[$i]['len'] = strlen($encoded);
        }

        $pairs = [];

        // 1 + 2: تطابق بعد التوحيد، وأخطاء إملائية بمسافة تحرير صغيرة
        usort($items, fn($a, $b) => $a['len'] <=> $b['len']);
        $n      = count($items);
        $maxGap = (100 - $minSimilarity) / 100;
        for ($i = 0; $i < $n; $i++) {
            $a = $items[$i];
            for ($j = $i + 1; $j < $n; $j++) {
                $b = $items[$j];
                // الفرق في الطول وحده يتجاوز المسموح، وما بعده أطول
                if ($b['len'] - $a['len'] > $maxGap * $b['len']) {
                    break;
                }
                if ($a['enc'] === $b['enc']) {
                    $pairs[] = [$a, $b, self::TYPE_NORMALIZED, 100];
                    continue;
                }
                $dist = levenshtein($a['enc'], $b['enc']);
                $sim  = (int) floor((1 - $dist / max($a['len'], $b['len'])) * 100);
                if ($sim >= $minSimilarity) {
                    $pairs[] = [$a, $b, self::TYPE_SPELLING, $sim];
                }
            }
        }

        // 3: اسم ناقص — نفس الاسم الأول والأخير، وكلمات أحدهما متضمّنة بالترتيب في الآخر
        if ($includePartial) {
            $buckets = [];
            foreach ($items as $it) {
                if (count($it['keys']) >= 2) {
                    $buckets[$it['keys'][0] . '|' . end($it['keys'])][] = $it;
                }
            }
            foreach ($buckets as $bucket) {
                $c = count($bucket);
                for ($i = 0; $i < $c; $i++) {
                    for ($j = $i + 1; $j < $c; $j++) {
                        [$s, $l] = count($bucket[$i]['keys']) <= count($bucket[$j]['keys'])
                            ? [$bucket[$i], $bucket[$j]] : [$bucket[$j], $bucket[$i]];
                        if (count($s['keys']) === count($l['keys']) || ! self::isSubsequence($s['keys'], $l['keys'])) {
                            continue;
                        }
                        $sim = (int) floor(count($s['keys']) / count($l['keys']) * 100);
                        $pairs[] = [$s, $l, self::TYPE_PARTIAL, $sim];
                    }
                }
            }
        }

        // كتابتان قد تلتقيان بأكثر من طريقة (خطأ إملائي واسم ناقص معاً) — نجمعهما في مجموعة واحدة
        $groups = [];
        foreach ($pairs as [$a, $b, $type, $sim]) {
            [$a, $b] = min($a['ids']) <= min($b['ids']) ? [$a, $b] : [$b, $a];
            $key = $a['raw'] . '|' . $b['raw'];
            $groups[$key] ??= ['sides' => [$a['ids'], $b['ids']], 'types' => []];
            $groups[$key]['types'][$type] = max($groups[$key]['types'][$type] ?? 0, $sim);
        }

        return array_values(array_map(function ($g) {
            $ids = array_merge(...$g['sides']);
            sort($ids);
            return $g + ['ids' => $ids];
        }, $groups));
    }

    /**
     * يقيّم مجموعة بالقرائن من باقي الحقول (الهوية، الهاتف، اسم الأم، العمر)
     * لأن الإخوة يتشابهون في الاسم الثلاثي ويختلفون في العمر والهوية.
     *
     * @param  Collection  $members  الأعضاء مفهرسين بالـ id
     */
    public function assess(array $group, Collection $members): array
    {
        $base = max(array_map(fn($type, $sim) => match ($type) {
            self::TYPE_NORMALIZED => 90,
            self::TYPE_SPELLING   => $sim - 5,
            default               => 65,
        }, array_keys($group['types']), $group['types']));

        // عند وجود نسخ مكررة من إحدى الكتابتين نأخذ أقوى زوج، وقرائنه هي ما يُعرض
        $best = null;
        foreach ($group['sides'][0] as $aId) {
            foreach ($group['sides'][1] as $bId) {
                $ev = $this->pairEvidence($members[$aId], $members[$bId]);
                $ev['score'] = max(0, min(100, $base + $ev['delta']));
                if ($best === null || $ev['score'] > $best['score']) {
                    $best = $ev;
                }
            }
        }

        // الكلمات التي لا توجد في الكتابة الأخرى — تُظلَّل في العرض
        $sideTokens = array_map(fn($ids) => self::tokens($members[$ids[0]]->full_name), $group['sides']);
        $tokens     = [];
        $differing  = [];
        foreach ($group['sides'] as $side => $ids) {
            $otherKeys = array_column($sideTokens[1 - $side], 'key');
            foreach ($ids as $id) {
                $tokens[$id] = $sideTokens[$side];
                foreach ($sideTokens[$side] as $t) {
                    if (! in_array($t['key'], $otherKeys, true)) {
                        $differing[$id][$t['key']] = true;
                    }
                }
            }
        }

        return [
            'members'   => collect($group['ids'])->map(fn($id) => $members[$id]),
            'tokens'    => $tokens,
            'differing' => $differing,
            'types'     => $group['types'],
            'score'     => $best['score'],
            'level'     => $best['score'] >= 85 ? 'high' : ($best['score'] >= 65 ? 'medium' : 'low'),
            'positive'  => array_values($best['positive']),
            'negative'  => array_values($best['negative']),
        ];
    }

    private function pairEvidence($a, $b): array
    {
        $positive = [];
        $negative = [];
        $delta    = 0;

        $nidA = trim((string) $a->national_id);
        $nidB = trim((string) $b->national_id);
        if ($nidA !== '' && $nidB !== '') {
            if ($nidA === $nidB) {
                $positive['nid'] = 'نفس رقم الهوية';
                $delta += 30;
            } else {
                $negative['nid'] = 'رقم الهوية مختلف';
                $delta -= 30;
            }
        }

        $phonesA = array_filter([self::phoneKey($a->phone), self::phoneKey($a->phone2)]);
        $phonesB = array_filter([self::phoneKey($b->phone), self::phoneKey($b->phone2)]);
        if (array_intersect($phonesA, $phonesB)) {
            $positive['phone'] = 'نفس رقم الهاتف';
            $delta += 20;
        }

        $motherA = self::normalize($a->mother_name);
        $motherB = self::normalize($b->mother_name);
        if ($motherA !== '' && $motherB !== '') {
            $sim = self::similarity($motherA, $motherB);
            if ($sim >= 85) {
                // الإخوة يشتركون في اسم الأم، لذلك وزنه خفيف
                $positive['mother'] = 'اسم الأم متطابق';
                $delta += 5;
            } elseif ($sim < 70) {
                $negative['mother'] = 'اسم الأم مختلف';
                $delta -= 25;
            }
        }

        if ($a->age && $b->age) {
            $gap = abs($a->age - $b->age);
            if ($gap <= 2) {
                $positive['age'] = 'العمر متقارب';
                $delta += 10;
            } elseif ($gap >= 6) {
                $negative['age'] = 'فرق العمر كبير';
                $delta -= 25;
            }
        }

        return compact('positive', 'negative', 'delta');
    }

    private static function phoneKey(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        return strlen($digits) >= 7 ? substr($digits, -9) : null;
    }

    /** نسبة التشابه (0–100) بين نصين موحّدين، على مستوى الحرف */
    public static function similarity(string $a, string $b): int
    {
        $a = mb_str_split(str_replace(' ', '', $a));
        $b = mb_str_split(str_replace(' ', '', $b));
        $max = max(count($a), count($b));
        if ($max === 0) {
            return 100;
        }
        $map = array_flip(array_values(array_unique(array_merge($a, $b))));
        $enc = fn($chars) => implode('', array_map(fn($c) => chr(($map[$c] % 255) + 1), $chars));

        return (int) floor((1 - levenshtein($enc($a), $enc($b)) / $max) * 100);
    }

    private static function isSubsequence(array $short, array $long): bool
    {
        $k = 0;
        foreach ($long as $word) {
            if ($k < count($short) && $short[$k] === $word) {
                $k++;
            }
        }
        return $k === count($short);
    }
}

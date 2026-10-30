<?php

use App\Services\SimilarNameFinder;

function similarMember(int $id, string $name, array $extra = []): object
{
    return (object) array_merge([
        'id' => $id, 'full_name' => $name, 'national_id' => null, 'phone' => null,
        'phone2' => null, 'mother_name' => null, 'age' => null,
    ], $extra);
}

function pairTypes(array $groups): array
{
    return collect($groups)->flatMap(fn($g) => array_keys($g['types']))->unique()->values()->all();
}

test('normalize unifies hamza, taa marbuta, alef maqsura and diacritics', function () {
    expect(SimilarNameFinder::normalize('أحْمَد إبراهيم'))->toBe('احمد ابراهيم')
        ->and(SimilarNameFinder::normalize('فاطمة'))->toBe(SimilarNameFinder::normalize('فاطمه'))
        ->and(SimilarNameFinder::normalize('مصطفى'))->toBe('مصطفي')
        ->and(SimilarNameFinder::normalize('  آمنة   يوسف '))->toBe('امنه يوسف');
});

test('compound names match with or without the space', function () {
    $keys = fn($n) => array_column(SimilarNameFinder::tokens($n), 'key');

    expect($keys('عادل عبد الرزاق عثمان'))->toBe($keys('عادل عبدالرزاق عثمان'))
        ->and($keys('جورية علي عطا الله'))->toBe($keys('جورية علي عطاالله'))
        ->and($keys('محي الدين البدر'))->toHaveCount(2);
});

test('finds spelling variants, typos and missing middle names', function () {
    $finder = new SimilarNameFinder();

    expect(pairTypes($finder->findGroups(collect([similarMember(1, 'آمنة يوسف أحمد'), similarMember(2, 'امنه يوسف احمد')]))))
        ->toBe([SimilarNameFinder::TYPE_NORMALIZED]);

    expect(pairTypes($finder->findGroups(collect([similarMember(1, 'سميرة محمد مطر'), similarMember(2, 'سميره محمد مطير')]))))
        ->toBe([SimilarNameFinder::TYPE_SPELLING]);

    expect(pairTypes($finder->findGroups(collect([similarMember(1, 'سمر برغش'), similarMember(2, 'سمر احمد برغش')]))))
        ->toBe([SimilarNameFinder::TYPE_PARTIAL]);
});

test('ignores unrelated names and exact copies covered by the main duplicates page', function () {
    $finder = new SimilarNameFinder();

    expect($finder->findGroups(collect([similarMember(1, 'يوسف دباس'), similarMember(2, 'غسان محمد صالحه')])))->toBe([])
        ->and($finder->findGroups(collect([similarMember(1, 'ريم فتحي محفوظ'), similarMember(2, 'ريم فتحي محفوظ')])))->toBe([]);
});

test('an exact copy joins the group when another spelling of it exists', function () {
    $groups = (new SimilarNameFinder())->findGroups(collect([
        similarMember(1, 'هناء محمود مراد'), similarMember(2, 'هناء محمود مراد'), similarMember(3, 'هناء محمود مرادي'),
    ]));

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['ids'])->toBe([1, 2, 3]);
});

test('a chain of similar names stays as separate pairs so siblings are not merged', function () {
    $groups = (new SimilarNameFinder())->findGroups(collect([
        similarMember(1, 'داليه محمد المصري'), similarMember(2, 'داليا محمد المصري'), similarMember(3, 'داليا محمد المصريه'),
    ]));

    expect(collect($groups)->pluck('ids')->all())->toEqualCanonicalizing([[1, 2], [2, 3], [1, 3]]);
});

test('evidence raises same-person matches and lowers likely siblings', function () {
    $finder = new SimilarNameFinder();
    $assess = function (array $a, array $b) use ($finder) {
        $members = collect([similarMember(1, 'سميرة محمد مطر', $a), similarMember(2, 'سميره محمد مطير', $b)])->keyBy('id');
        return $finder->assess($finder->findGroups($members)[0], $members);
    };

    $same = $assess(['phone' => '0959067679', 'age' => 40], ['phone' => '959067679', 'age' => 41]);
    expect($same['level'])->toBe('high')
        ->and($same['positive'])->toContain('نفس رقم الهاتف', 'العمر متقارب');

    $siblings = $assess(['national_id' => '111', 'age' => 30], ['national_id' => '222', 'age' => 45]);
    expect($siblings['level'])->toBe('low')
        ->and($siblings['negative'])->toContain('رقم الهوية مختلف', 'فرق العمر كبير');
});

test('marks only the words that differ', function () {
    $finder  = new SimilarNameFinder();
    $members = collect([similarMember(1, 'محمد علي حسن'), similarMember(2, 'محمد علي حسين')])->keyBy('id');
    $group   = $finder->assess($finder->findGroups($members)[0], $members);

    expect(array_keys($group['differing'][1]))->toBe(['حسن'])
        ->and(array_keys($group['differing'][2]))->toBe(['حسين']);
});

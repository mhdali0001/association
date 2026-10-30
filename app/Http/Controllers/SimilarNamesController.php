<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SimilarNameDismissal;
use App\Models\VerificationStatus;
use App\Services\ActivityLogger;
use App\Services\SimilarNameFinder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SimilarNamesController extends Controller
{
    private const PER_PAGE = 30;

    public function index(Request $request, SimilarNameFinder $finder)
    {
        $type           = $request->get('type', 'all');     // all | normalized | spelling | partial
        $level          = $request->get('level', 'all');    // all | high | medium
        $similarity     = (int) $request->get('similarity', 85);
        $similarity     = in_array($similarity, [75, 80, 85, 90, 95], true) ? $similarity : 85;
        $verificationId = $request->get('verification_status_id');
        $search         = trim($request->get('search', ''));
        $showDismissed  = $request->get('show') === 'dismissed';

        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();

        $members = Member::with(['verificationStatus', 'region'])
            ->whereNotNull('full_name')
            ->where('full_name', '!=', '')
            ->get()
            ->keyBy('id');

        // المقارنة الثنائية تكلّف قرابة ثانية — نخزّنها حتى تتغير بيانات الأعضاء
        $signature = $members->count() . '|' . $members->max('updated_at') . '|' . $similarity;
        $rawGroups = Cache::remember('similar-name-pairs:' . md5($signature), now()->addHours(6),
            fn () => $finder->findGroups($members, $similarity));

        $dismissals = SimilarNameDismissal::with('user')->get()
            ->keyBy(fn($d) => SimilarNameDismissal::key($d->member_a_id, $d->member_b_id));

        $groups = collect($rawGroups)
            ->filter(fn($g) => collect($g['ids'])->every(fn($id) => $members->has($id)))
            ->map(function ($g) use ($finder, $members, $dismissals) {
                // المجموعة متجاهلة فقط إذا تجوهلت كل أزواجها — سجل جديد بنفس الكتابة يعيدها للمراجعة
                $pairDismissals = [];
                foreach ($g['sides'][0] as $a) {
                    foreach ($g['sides'][1] as $b) {
                        $pairDismissals[] = $dismissals->get(SimilarNameDismissal::key($a, $b));
                    }
                }
                $dismissed = ! in_array(null, $pairDismissals, true);

                return $finder->assess($g, $members) + [
                    'sides'     => $g['sides'],
                    'dismissal' => $dismissed ? collect($pairDismissals)->sortByDesc('created_at')->first() : null,
                ];
            });

        $dismissedCount = $groups->whereNotNull('dismissal')->count();
        $groups         = $showDismissed ? $groups->whereNotNull('dismissal') : $groups->whereNull('dismissal');

        $counts = [
            'total'      => $groups->count(),
            'high'       => $groups->where('level', 'high')->count(),
            'normalized' => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_NORMALIZED]))->count(),
            'spelling'   => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_SPELLING]))->count(),
            'partial'    => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_PARTIAL]))->count(),
            'dismissed'  => $dismissedCount,
        ];

        if (in_array($type, [SimilarNameFinder::TYPE_NORMALIZED, SimilarNameFinder::TYPE_SPELLING, SimilarNameFinder::TYPE_PARTIAL], true)) {
            $groups = $groups->filter(fn($g) => isset($g['types'][$type]));
        }
        if ($level === 'high') {
            $groups = $groups->where('level', 'high');
        } elseif ($level === 'medium') {
            $groups = $groups->whereIn('level', ['high', 'medium']);
        }
        if ($verificationId) {
            $groups = $groups->filter(fn($g) => $g['members']->contains('verification_status_id', (int) $verificationId));
        }
        if ($search !== '') {
            $needle = SimilarNameFinder::normalize($search);
            $groups = $groups->filter(fn($g) => $g['members']->contains(fn($m) =>
                str_contains(SimilarNameFinder::normalize($m->full_name), $needle)
                || str_contains((string) $m->national_id, $search)
                || str_contains((string) $m->phone, $search)
            ));
        }

        $groups = $showDismissed
            ? $groups->sortByDesc(fn($g) => $g['dismissal']->created_at)->values()
            : $groups->sortByDesc('score')->values();

        $page      = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $groups->forPage($page, self::PER_PAGE)->values(),
            $groups->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('members.similar-names', [
            'groups'               => $paginated,
            'counts'               => $counts,
            'type'                 => $type,
            'level'                => $level,
            'similarity'           => $similarity,
            'verificationId'       => $verificationId,
            'search'               => $search,
            'showDismissed'        => $showDismissed,
            'verificationStatuses' => $verificationStatuses,
        ]);
    }

    /** «ليسا تكراراً» — يخفي المجموعة بتسجيل كل أزواجها */
    public function dismiss(Request $request)
    {
        [$sideA, $sideB] = $this->validatedSides($request);

        DB::transaction(function () use ($sideA, $sideB) {
            foreach ($sideA as $a) {
                foreach ($sideB as $b) {
                    SimilarNameDismissal::firstOrCreate(
                        ['member_a_id' => min($a, $b), 'member_b_id' => max($a, $b)],
                        ['user_id' => auth()->id()]
                    );
                }
            }
        });

        $names = $this->namesOf($sideA, $sideB);
        ActivityLogger::log('reviewed', "تجاهل تشابه الأسماء: «{$names[0]}» و«{$names[1]}» ليسا تكراراً",
            Member::find($sideA[0]), ['member_ids' => array_merge($sideA, $sideB)]);

        return $this->respond($request, 'تم تجاهل المجموعة — لن تظهر مجدداً إلا إذا أُضيف سجل جديد بنفس الاسم');
    }

    public function restore(Request $request)
    {
        [$sideA, $sideB] = $this->validatedSides($request);

        SimilarNameDismissal::where(function ($q) use ($sideA, $sideB) {
            foreach ($sideA as $a) {
                foreach ($sideB as $b) {
                    $q->orWhere(fn($q2) => $q2->where('member_a_id', min($a, $b))->where('member_b_id', max($a, $b)));
                }
            }
        })->delete();

        $names = $this->namesOf($sideA, $sideB);
        ActivityLogger::log('reviewed', "إلغاء تجاهل تشابه الأسماء: «{$names[0]}» و«{$names[1]}»",
            Member::find($sideA[0]), ['member_ids' => array_merge($sideA, $sideB)]);

        return $this->respond($request, 'تمت إعادة المجموعة إلى قائمة المراجعة');
    }

    private function validatedSides(Request $request): array
    {
        $data = $request->validate([
            'a'   => ['required', 'array', 'max:50'],
            'a.*' => ['integer', 'exists:members,id'],
            'b'   => ['required', 'array', 'max:50'],
            'b.*' => ['integer', 'exists:members,id'],
        ]);
        $sideA = array_values(array_unique(array_map('intval', $data['a'])));
        $sideB = array_values(array_unique(array_map('intval', $data['b'])));
        abort_if(array_intersect($sideA, $sideB), 422, 'لا يمكن أن يكون العضو في الطرفين');

        return [$sideA, $sideB];
    }

    private function namesOf(array $sideA, array $sideB): array
    {
        return [Member::whereKey($sideA[0])->value('full_name'), Member::whereKey($sideB[0])->value('full_name')];
    }

    private function respond(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }
}

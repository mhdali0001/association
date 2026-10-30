<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\VerificationStatus;
use App\Services\SimilarNameFinder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

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

        $groups = collect($rawGroups)
            ->filter(fn($g) => collect($g['ids'])->every(fn($id) => $members->has($id)))
            ->map(fn($g) => $finder->assess($g, $members));

        $counts = [
            'total'      => $groups->count(),
            'high'       => $groups->where('level', 'high')->count(),
            'normalized' => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_NORMALIZED]))->count(),
            'spelling'   => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_SPELLING]))->count(),
            'partial'    => $groups->filter(fn($g) => isset($g['types'][SimilarNameFinder::TYPE_PARTIAL]))->count(),
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

        $groups = $groups->sortByDesc('score')->values();

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
            'verificationStatuses' => $verificationStatuses,
        ]);
    }
}

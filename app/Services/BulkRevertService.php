<?php

namespace App\Services;

use App\Models\BulkRevertItem;
use App\Models\BulkRevertSession;
use App\Models\Member;
use App\Models\MemberScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulkRevertService
{
    public static function capture(array $memberIds, string $operation, string $description): BulkRevertSession
    {
        $members = Member::with('scores')->whereIn('id', $memberIds)->get();

        $session = BulkRevertSession::create([
            'user_id'        => Auth::id(),
            'operation'      => $operation,
            'description'    => $description,
            'affected_count' => $members->count(),
        ]);

        $now = now();
        $items = $members->map(fn($m) => [
            'session_id'      => $session->id,
            'member_id'       => $m->id,
            'member_snapshot' => json_encode($m->only($m->getFillable())),
            'scores_snapshot' => $m->scores ? json_encode($m->scores->only($m->scores->getFillable())) : null,
            'created_at'      => $now,
        ])->toArray();

        foreach (array_chunk($items, 500) as $chunk) {
            BulkRevertItem::insert($chunk);
        }

        return $session;
    }

    public static function revert(BulkRevertSession $session): int
    {
        if ($session->isReverted()) {
            return 0;
        }

        $count = 0;

        DB::transaction(function () use ($session, &$count) {
            $items = $session->items()->get();

            foreach ($items as $item) {
                $member = Member::find($item->member_id);
                if (!$member) continue;

                if ($item->member_snapshot) {
                    $snap = is_array($item->member_snapshot) ? $item->member_snapshot : json_decode($item->member_snapshot, true);
                    $member->fill(array_intersect_key($snap, array_flip($member->getFillable())))->save();
                }

                if ($item->scores_snapshot) {
                    $snap = is_array($item->scores_snapshot) ? $item->scores_snapshot : json_decode($item->scores_snapshot, true);
                    $scores = $member->scores ?? new MemberScore(['member_id' => $member->id]);
                    $scores->fill(array_intersect_key($snap, array_flip($scores->getFillable())))->save();
                }

                $count++;
            }

            $session->update([
                'reverted_at' => now(),
                'reverted_by' => Auth::id(),
            ]);

            ActivityLogger::log('updated', "تراجع جماعي: {$session->description} ({$count} مستفيد)");
        });

        return $count;
    }
}

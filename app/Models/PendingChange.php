<?php

namespace App\Models;

use App\Models\Association;
use App\Models\Donation;
use App\Models\MaritalStatus;
use App\Models\MemberImage;
use App\Models\MemberScore;
use App\Models\PaymentInfoAI;
use App\Models\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PendingChange extends Model
{
    protected $fillable = [
        'model_type', 'model_id', 'action', 'payload', 'original',
        'requested_by', 'status', 'reviewed_by', 'reviewed_at', 'reviewer_notes',
    ];

    protected $casts = [
        'payload'     => 'array',
        'original'    => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function memberSnapshots()
    {
        return $this->hasMany(PendingChangeMember::class);
    }

    /**
     * Create a bulk PendingChange and populate per-member snapshots.
     */
    public static function createWithSnapshots(array $data, array $memberIds): self
    {
        $change = self::create($data);
        $change->populateMemberSnapshots($memberIds);
        return $change;
    }

    public function populateMemberSnapshots(array $memberIds): void
    {
        if (empty($memberIds)) return;

        $action  = $this->action;
        $payload = $this->payload ?? [];

        // Fields needed from members table beyond the basics
        $selectFields = ['id', 'full_name', 'dossier_number', 'score', 'phone'];
        $withScores   = false;

        if ($action === 'bulk_update') {
            $changedFields = array_keys($payload['fields'] ?? []);
            $memberTableFields = array_intersect($changedFields, [
                'network', 'marital_status', 'current_address', 'region_id', 'sector_id',
                'housing_status_id', 'verification_status_id', 'estimated_amount',
                'payments_count', 'delegate', 'final_status_id', 'sham_cash_account',
            ]);
            $selectFields = array_unique(array_merge($selectFields, $memberTableFields));
        } elseif (in_array($action, ['bulk_score_addition', 'bulk_score_deduction', 'bulk_score_equalize'])) {
            $withScores = true;
        }

        $query = Member::whereIn('id', $memberIds)->select($selectFields);
        if ($withScores) $query->with('scores');

        $now  = now()->toDateTimeString();
        $rows = [];

        foreach ($query->cursor() as $member) {
            [$before, $after] = self::buildSnapshots($member, $action, $payload);
            $rows[] = [
                'pending_change_id' => $this->id,
                'member_id'         => $member->id,
                'full_name'         => $member->full_name,
                'dossier_number'    => $member->dossier_number,
                'before'            => json_encode($before),
                'after'             => json_encode($after),
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            if (count($rows) >= 300) {
                DB::table('pending_change_members')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('pending_change_members')->insert($rows);
        }
    }

    private static function buildSnapshots(Member $member, string $action, array $payload): array
    {
        $scores = $member->relationLoaded('scores') ? $member->scores : null;

        $rawScore = $scores
            ? ((int)($scores->work_score ?? 0)
               + (int)($scores->housing_score ?? 0)
               + (int)($scores->dependents_score ?? 0) + (int)($scores->dependent_status_score ?? 0)
               + (int)($scores->illness_score ?? 0) + (int)($scores->special_cases_score ?? 0))
            : (int)($member->score ?? 0);

        switch ($action) {
            case 'bulk_delete':
                return [
                    ['score' => $member->score, 'phone' => $member->phone],
                    null,
                ];

            case 'bulk_update':
                $fields = $payload['fields'] ?? [];
                $before = [];
                foreach ($fields as $field => $newVal) {
                    $before[$field] = $member->{$field} ?? null;
                }
                return [$before, $fields];

            case 'bulk_score_addition':
                $addition  = (int)($payload['score_addition'] ?? 0);
                $deduction = (int)($scores?->score_deduction ?? 0);
                $newTotal  = max(0, $rawScore + $addition - $deduction);
                return [
                    ['score' => (int)$member->score, 'score_addition' => (int)($scores?->score_addition ?? 0)],
                    ['score' => $newTotal,            'score_addition' => $addition],
                ];

            case 'bulk_score_deduction':
                $deduction = (int)($payload['score_deduction'] ?? 0);
                $addition  = (int)($scores?->score_addition ?? 0);
                $newTotal  = max(0, $rawScore + $addition - $deduction);
                return [
                    ['score' => (int)$member->score, 'score_deduction' => (int)($scores?->score_deduction ?? 0)],
                    ['score' => $newTotal,            'score_deduction' => $deduction],
                ];

            case 'bulk_score_equalize':
                $target = (int)($payload['target_score'] ?? 0);
                return [
                    ['score' => (int)$member->score],
                    ['score' => $target],
                ];

            default:
                return [null, null];
        }
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    /** Arabic action label */
    public function actionLabel(): string
    {
        return match($this->action) {
            'create'       => 'إضافة',
            'update'       => 'تعديل',
            'delete'       => 'حذف',
            'bulk_amount'           => 'تعديل جماعي للمبلغ',
            'bulk_score_deduction'  => 'انقاص جماعي للنقاط',
            'bulk_score_addition'   => 'إضافة جماعية للنقاط',
            'bulk_score_equalize'   => 'تسوية جماعية للنقاط',
            'bulk_delete'           => 'حذف جماعي',
            'bulk_update'           => 'تعديل جماعي',
            default        => $this->action,
        };
    }

    /** Arabic model label */
    public function modelLabel(): string
    {
        return match($this->model_type) {
            'member'              => 'مستفيد',
            'donation'            => 'تبرع',
            'member_image'        => 'ملف / صورة',
            'marital_status'      => 'حالة اجتماعية',
            'association'         => 'جمعية',
            'verification_status' => 'حالة تحقق',
            'field_visit'         => 'جولة ميدانية',
            'region'              => 'منطقة',
            default               => $this->model_type,
        };
    }

    /** A short description for list views */
    public function summary(): string
    {
        if (in_array($this->action, ['bulk_amount', 'bulk_score_deduction', 'bulk_score_addition', 'bulk_score_equalize'])) {
            return "{$this->actionLabel()}: " . ($this->payload['label'] ?? '');
        }

        if ($this->action === 'bulk_delete') {
            $count   = $this->payload['count'] ?? count($this->payload['member_ids'] ?? []);
            $preview = implode('، ', array_slice($this->payload['names_preview'] ?? [], 0, 3));
            $extra   = $count > 3 ? ' و' . ($count - 3) . ' آخرين' : '';
            return "حذف جماعي لـ {$count} مستفيد: {$preview}{$extra}";
        }

        if ($this->action === 'bulk_update') {
            $count   = $this->payload['count'] ?? count($this->payload['member_ids'] ?? []);
            $fields  = array_keys($this->payload['fields'] ?? []);
            $labels  = self::memberFieldLabels();
            $fNames  = implode('، ', array_map(fn($f) => $labels[$f] ?? $f, $fields));
            return "تعديل جماعي لـ {$count} مستفيد — الحقول: {$fNames}";
        }

        $name = match($this->model_type) {
            'member'              => $this->payload['full_name']   ?? $this->getAttribute('original')['full_name']   ?? "#{$this->model_id}",
            'donation'            => $this->payload['member_name'] ?? $this->getAttribute('original')['member_name'] ?? "#{$this->model_id}",
            'member_image'        => $this->payload['member_name'] ?? $this->getAttribute('original')['member_name'] ?? "#{$this->model_id}",
            'field_visit'         => $this->payload['member_name'] ?? $this->getAttribute('original')['member_name'] ?? "#{$this->model_id}",
            'marital_status',
            'association',
            'verification_status',
            'region'              => $this->payload['name'] ?? $this->getAttribute('original')['name'] ?? "#{$this->model_id}",
            default               => "#{$this->model_id}",
        };
        return "{$this->actionLabel()} {$this->modelLabel()}: {$name}";
    }

    /**
     * Apply the pending change to the database.
     * Only call this after confirming the user is an admin.
     */
    public function apply(): void
    {
        if ($this->action === 'bulk_amount') {
            $this->applyBulkAmount();
        } elseif ($this->action === 'bulk_score_deduction') {
            $this->applyBulkScoreDeduction();
        } elseif ($this->action === 'bulk_score_addition') {
            $this->applyBulkScoreAddition();
        } elseif ($this->action === 'bulk_score_equalize') {
            $this->applyBulkScoreEqualize();
        } elseif ($this->action === 'bulk_delete') {
            $this->applyBulkDelete();
        } elseif ($this->action === 'bulk_update') {
            $this->applyBulkUpdate();
        } else {
            match($this->model_type) {
                'member'              => $this->applyMember(),
                'donation'            => $this->applyDonation(),
                'member_image'        => $this->applyMemberImage(),
                'marital_status'      => $this->applyMaritalStatus(),
                'association'         => $this->applyAssociation(),
                'verification_status' => $this->applyVerificationStatus(),
                'field_visit'         => $this->applyFieldVisit(),
                'region'              => $this->applyRegion(),
                default               => throw new \RuntimeException("Unknown model type: {$this->model_type}"),
            };
        }

        $this->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
    }

    private function applyBulkDelete(): void
    {
        $ids = $this->payload['member_ids'] ?? [];
        if (empty($ids)) return;
        Member::whereIn('id', $ids)->delete();
    }

    private function applyBulkUpdate(): void
    {
        $ids    = $this->payload['member_ids'] ?? [];
        $fields = $this->payload['fields']     ?? [];
        if (empty($ids) || empty($fields)) return;

        if (array_key_exists('field_visit_status_id', $fields)) {
            $fvsId = $fields['field_visit_status_id'];
            unset($fields['field_visit_status_id']);
            foreach ($ids as $memberId) {
                $visit = \App\Models\FieldVisit::where('member_id', $memberId)->latest()->first();
                if ($visit) {
                    $visit->update(['field_visit_status_id' => $fvsId]);
                } else {
                    \App\Models\FieldVisit::create(['member_id' => $memberId, 'field_visit_status_id' => $fvsId]);
                }
            }
        }

        if (array_key_exists('fv_visitor', $fields)) {
            $visitor = $fields['fv_visitor'];
            unset($fields['fv_visitor']);
            foreach ($ids as $memberId) {
                $visit = \App\Models\FieldVisit::where('member_id', $memberId)->latest()->first();
                if ($visit) {
                    $visit->update(['visitor' => $visitor]);
                }
            }
        }

        if (array_key_exists('payment_data_entry_name', $fields)) {
            $payDeName = $fields['payment_data_entry_name'];
            unset($fields['payment_data_entry_name']);
            foreach ($ids as $memberId) {
                \App\Models\PaymentInfo::where('member_id', $memberId)
                    ->update(['data_entry_name' => $payDeName ?: null]);
            }
        }

        if (!empty($fields)) {
            Member::whereIn('id', $ids)->update($fields);
        }
    }

    private function applyBulkAmount(): void
    {
        $p         = $this->payload ?? [];
        $operation = $p['operation'];
        $amount    = (float) $p['amount'];
        $ids       = $p['member_ids'] ?? [];
        $field = 'estimated_amount';

        if (empty($ids)) {
            return;
        }

        $query = Member::whereIn('id', $ids);

        switch ($operation) {
            case 'add':
                $query->update([$field => \Illuminate\Support\Facades\DB::raw('COALESCE(' . $field . ', 0) + ' . $amount)]);
                break;
            case 'subtract':
                $query->update([$field => \Illuminate\Support\Facades\DB::raw('GREATEST(COALESCE(' . $field . ', 0) - ' . $amount . ', 0)')]);
                break;
            default: // set
                $query->update([$field => $amount]);
        }
    }

    private function applyBulkScoreDeduction(): void
    {
        $p         = $this->payload ?? [];
        $deduction = (int) ($p['score_deduction'] ?? 0);
        $reason    = $p['score_deduction_reason'] ?? null;
        $ids       = $p['member_ids'] ?? [];

        if (empty($ids)) return;

        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $addition   = (int)($scores->score_addition ?? 0);
            $totalScore = max(0, $rawScore + $addition - $deduction);

            $scores->fill([
                'member_id'              => $memberId,
                'score_deduction'        => $deduction,
                'score_deduction_reason' => $reason,
                'total_score'            => $totalScore,
            ])->save();

            $member->update([
                'score'            => $totalScore,
                'estimated_amount' => $totalScore * 500,
            ]);
        }
    }

    private function applyBulkScoreAddition(): void
    {
        $p        = $this->payload ?? [];
        $addition = (int) ($p['score_addition'] ?? 0);
        $reason   = $p['score_addition_reason'] ?? null;
        $ids      = $p['member_ids'] ?? [];

        if (empty($ids)) return;

        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $deduction  = (int)($scores->score_deduction ?? 0);
            $totalScore = max(0, $rawScore + $addition - $deduction);

            $scores->fill([
                'member_id'             => $memberId,
                'score_addition'        => $addition,
                'score_addition_reason' => $reason,
                'total_score'           => $totalScore,
            ])->save();

            $member->update([
                'score'            => $totalScore,
                'estimated_amount' => $totalScore * 500,
            ]);
        }
    }

    private function applyBulkScoreEqualize(): void
    {
        $p      = $this->payload ?? [];
        $target = (int) ($p['target_score'] ?? 0);
        $reason = $p['reason'] ?? null;
        $ids    = $p['member_ids'] ?? [];

        if (empty($ids)) return;

        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $clamped = max(0, $target);
            if ($clamped >= $rawScore) {
                $addition  = $clamped - $rawScore;
                $deduction = 0;
            } else {
                $addition  = 0;
                $deduction = $rawScore - $clamped;
            }

            $scores->fill([
                'member_id'              => $memberId,
                'score_addition'         => $addition,
                'score_addition_reason'  => $addition > 0 ? $reason : null,
                'score_deduction'        => $deduction,
                'score_deduction_reason' => $deduction > 0 ? $reason : null,
                'total_score'            => $clamped,
            ])->save();

            $member->update([
                'score'            => $clamped,
                'estimated_amount' => $clamped * 500,
            ]);
        }
    }

    /**
     * Reverse a previously-applied change (called from revoke).
     * Best-effort: bulk and delete-type actions may not be fully reversible.
     */
    public function undo(): void
    {
        if (in_array($this->action, ['bulk_amount', 'bulk_score_deduction', 'bulk_score_addition', 'bulk_score_equalize', 'bulk_delete', 'bulk_update'])) {
            return; // bulk operations are not automatically reversible
        }

        match($this->model_type) {
            'field_visit'         => $this->undoFieldVisit(),
            'member'              => $this->undoMember(),
            'donation'            => $this->undoDonation(),
            'member_image'        => $this->undoMemberImage(),
            default               => null,
        };
    }

    private function undoFieldVisit(): void
    {
        $p      = $this->payload  ?? [];
        $o      = $this->getAttribute('original') ?? [];
        $member = \App\Models\Member::find($p['member_id'] ?? $o['member_id'] ?? null);

        if ($this->action === 'create') {
            \App\Models\FieldVisit::find($this->model_id)?->delete();
        } elseif ($this->action === 'update') {
            $visit = \App\Models\FieldVisit::find($this->model_id);
            if ($visit && !empty($o)) {
                $visit->update([
                    'field_visit_status_id' => $o['field_visit_status_id'] ?? null,
                    'house_type_id'         => $o['house_type_id']         ?? null,
                    'house_condition_id'    => $o['house_condition_id']    ?? null,
                    'visit_date'            => $o['visit_date']            ?? null,
                    'visitor'               => $o['visitor']               ?? null,
                    'estimated_amount'      => $o['estimated_amount']      ?? null,
                    'amount_reason'         => $o['amount_reason']         ?? null,
                    'notes'                 => $o['notes']                 ?? null,
                    'has_video'             => (bool) ($o['has_video']          ?? false),
                    'has_special_case'      => (bool) ($o['has_special_case']   ?? false),
                ]);
            }
        } elseif ($this->action === 'delete') {
            // Re-create the deleted field visit from its payload
            if ($member) {
                $member->fieldVisits()->create([
                    'field_visit_status_id' => $p['field_visit_status_id'] ?? null,
                    'house_type_id'         => $p['house_type_id']         ?? null,
                    'house_condition_id'    => $p['house_condition_id']    ?? null,
                    'visit_date'            => $p['visit_date']            ?? null,
                    'visitor'               => $p['visitor']               ?? null,
                    'estimated_amount'      => $p['estimated_amount']      ?? null,
                    'amount_reason'         => $p['amount_reason']         ?? null,
                    'notes'                 => $p['notes']                 ?? null,
                    'has_video'             => (bool) ($p['has_video']          ?? false),
                    'has_special_case'      => (bool) ($p['has_special_case']   ?? false),
                ]);
            }
        }

    }

    private function undoMember(): void
    {
        $o = $this->getAttribute('original') ?? [];

        if ($this->action === 'create') {
            Member::find($this->model_id)?->delete();
            return;
        }

        if ($this->action !== 'update' || empty($o)) {
            return; // delete action: cannot restore (record is gone from DB)
        }

        $member = Member::find($this->model_id);
        if (!$member) return;

        // Restore top-level member fields
        $restorable = array_intersect_key($o, array_flip([
            'full_name', 'age', 'gender', 'mother_name', 'national_id',
            'verification_status_id', 'final_status_id', 'dossier_number',
            'current_address', 'region_id', 'sector_id', 'marital_status', 'disease_type',
            'phone', 'phone2', 'network', 'provider_status', 'job', 'housing_status_id',
            'dependents_count', 'payments_count', 'notes', 'illness_details', 'special_cases',
            'special_cases_description', 'sham_cash_account', 'other_association',
            'representative_id', 'data_entry_name', 'delegate', 'second_person', 'association_id', 'estimated_amount',
        ]));
        if (!empty($restorable)) {
            $member->update($restorable);
        }

        // Restore scores
        if (!empty($o['scores'])) {
            $s = $member->scores ?? new MemberScore(['member_id' => $member->id]);
            $s->fill(array_merge($o['scores'], ['member_id' => $member->id]))->save();
        }

        // Restore payment info
        if (!empty($o['payment'])) {
            $pay = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);
            $pay->fill(array_merge($o['payment'], ['member_id' => $member->id]))->save();
        }

        // Restore payment AI info
        if (!empty($o['payment_ai'])) {
            $payAI = $member->paymentInfoAI ?? new PaymentInfoAI(['member_id' => $member->id]);
            $payAI->fill(array_merge($o['payment_ai'], ['member_id' => $member->id]))->save();
        }
    }

    private function undoDonation(): void
    {
        $o = $this->getAttribute('original') ?? [];

        if ($this->action === 'create') {
            Donation::find($this->model_id)?->delete();
        } elseif ($this->action === 'update' && !empty($o)) {
            $donation = Donation::find($this->model_id);
            if ($donation) {
                $donation->update(array_intersect_key($o, array_flip([
                    'member_id', 'amount', 'donation_month', 'type',
                    'status', 'reference_number', 'notes',
                ])));
            }
        }
    }

    private function undoMemberImage(): void
    {
        $o = $this->getAttribute('original') ?? [];

        if ($this->action === 'create') {
            $img = MemberImage::find($this->model_id);
            if ($img) {
                Storage::disk('public')->delete($img->file_path);
                $img->delete();
            }
        } elseif ($this->action === 'update') {
            MemberImage::find($this->model_id)?->update(['title' => $o['title'] ?? null]);
        }
    }

    /** Delete uploaded file when a pending image-upload is rejected */
    public function cleanup(): void
    {
        if ($this->model_type === 'member_image' && $this->action === 'create') {
            $path = $this->payload['file_path'] ?? null;
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function applyMember(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            Member::find($this->model_id)?->delete();
            return;
        }

        // Scores
        $scores = $p['scores'] ?? [];
        $workScore            = (int)($scores['work_score']             ?? 0);
        $housingScore         = (int)($scores['housing_score']          ?? 0);
        $dependentsScore      = (int)($scores['dependents_score']       ?? 0);
        $dependentStatusScore = (int)($scores['dependent_status_score'] ?? 0);
        $illnessScore         = (int)($scores['illness_score']          ?? 0);
        $specialScore         = (int)($scores['special_cases_score']    ?? 0);
        $scoreDeduction       = max(0, (int)($scores['score_deduction'] ?? 0));
        $scoreDeductionReason = $scores['score_deduction_reason']       ?? null;
        $scoreAddition        = max(0, (int)($scores['score_addition']  ?? 0));
        $scoreAdditionReason  = $scores['score_addition_reason']        ?? null;
        $rawScore             = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;
        $totalScore           = max(0, $rawScore + $scoreAddition - $scoreDeduction);

        $memberData = [
            'full_name'                 => $p['full_name']                 ?? null,
            'age'                       => $p['age']                       ?? null,
            'gender'                    => $p['gender']                    ?? null,
            'mother_name'               => $p['mother_name']               ?? null,
            'national_id'               => $p['national_id']               ?? null,
            'verification_status_id'    => $p['verification_status_id']    ?? null,
            'final_status_id'           => $p['final_status_id']           ?? null,
            'dossier_number'            => $p['dossier_number']            ?? null,
            'current_address'           => $p['current_address']           ?? null,
            'region_id'                 => $p['region_id']                 ?? null,
            'sector_id'                 => $p['sector_id']                 ?? null,
            'marital_status'            => $p['marital_status']            ?? null,
            'disease_type'              => $p['disease_type']              ?? null,
            'other_association'         => $p['other_association']         ?? false,
            'phone'                     => $p['phone']                     ?? null,
            'phone2'                    => $p['phone2']                    ?? null,
            'representative_id'         => $p['representative_id']         ?? null,
            'data_entry_name'           => $p['data_entry_name']           ?? null,
            'delegate'                  => $p['delegate']                  ?? null,
            'second_person'             => $p['second_person']             ?? null,
            'association_id'            => $p['association_id']            ?? null,
            'network'                   => $p['network']                   ?? null,
            'provider_status'           => $p['provider_status']           ?? null,
            'job'                       => $p['job']                       ?? null,
            'housing_status_id'         => $p['housing_status_id']         ?? null,
            'dependents_count'          => $p['dependents_count']          ?? null,
            'payments_count'            => $p['payments_count']            ?? null,
            'notes'                     => $p['notes']                     ?? null,
            'illness_details'           => $p['illness_details']           ?? null,
            'special_cases'             => $p['special_cases']             ?? false,
            'special_cases_description' => $p['special_cases_description'] ?? null,
            'sham_cash_account'         => in_array($p['sham_cash_account'] ?? '', ['done','manual']) ? $p['sham_cash_account'] : null,
            'score'                     => $totalScore,
            'estimated_amount'          => $totalScore * 500,
            'latitude'                  => $p['latitude']  ?? null,
            'longitude'                 => $p['longitude'] ?? null,
        ];

        $scoresData = [
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
            'score_deduction'        => $scoreDeduction,
            'score_deduction_reason' => $scoreDeductionReason,
            'score_addition'         => $scoreAddition,
            'score_addition_reason'  => $scoreAdditionReason,
        ];

        $payment   = $p['payment']    ?? [];
        $paymentAI = $p['payment_ai'] ?? [];

        if ($this->action === 'create') {
            $member = Member::create($memberData);
            $this->updateQuietly(['model_id' => $member->id]);
            MemberScore::create(array_merge($scoresData, ['member_id' => $member->id]));
            PaymentInfo::create(array_merge([
                'member_id'       => $member->id,
                'iban'            => $payment['iban']            ?? null,
                'barcode'         => $payment['barcode']         ?? null,
                'iban_image'      => $payment['iban_image']      ?? null,
                'barcode_image'   => $payment['barcode_image']   ?? null,
                'recipient_name'  => $payment['recipient_name']  ?? null,
                'data_entry_name' => $payment['data_entry_name'] ?? null,
            ]));
            PaymentInfoAI::create([
                'member_id'      => $member->id,
                'iban'           => $paymentAI['iban']           ?? null,
                'barcode'        => $paymentAI['barcode']        ?? null,
                'recipient_name' => $paymentAI['recipient_name'] ?? null,
            ]);
            if (!empty($p['association_ids'])) {
                $member->associations()->sync($p['association_ids']);
            }
        } elseif ($this->action === 'update') {
            $member = Member::findOrFail($this->model_id);
            $member->update($memberData);

            $s = $member->scores ?? new MemberScore(['member_id' => $member->id]);
            $s->fill(array_merge($scoresData, ['member_id' => $member->id]))->save();

            $pay = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);
            $pay->fill([
                'member_id'       => $member->id,
                'iban'            => $payment['iban']            ?? $pay->iban,
                'barcode'         => $payment['barcode']         ?? $pay->barcode,
                'iban_image'      => $payment['iban_image']      ?? $pay->iban_image,
                'barcode_image'   => $payment['barcode_image']   ?? $pay->barcode_image,
                'recipient_name'  => $payment['recipient_name']  ?? $pay->recipient_name,
                'data_entry_name' => $payment['data_entry_name'] ?? $pay->data_entry_name,
            ])->save();

            $payAI = $member->paymentInfoAI ?? new PaymentInfoAI(['member_id' => $member->id]);
            $payAI->fill([
                'member_id'      => $member->id,
                'iban'           => $paymentAI['iban']           ?? $payAI->iban,
                'barcode'        => $paymentAI['barcode']        ?? $payAI->barcode,
                'recipient_name' => $paymentAI['recipient_name'] ?? $payAI->recipient_name,
            ])->save();

            if (isset($p['association_ids'])) {
                $member->associations()->sync($p['association_ids']);
            }
        }
    }

    private function applyDonation(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            Donation::find($this->model_id)?->delete();
            return;
        }

        $data = [
            'member_id'        => $p['member_id'],
            'amount'           => $p['amount'],
            'donation_month'   => $p['donation_month'],
            'type'             => $p['type'],
            'status'           => $p['status'],
            'reference_number' => $p['reference_number'] ?? null,
            'notes'            => $p['notes']            ?? null,
            'user_id'          => $p['user_id']          ?? Auth::id(),
        ];

        if ($this->action === 'create') {
            $donation = Donation::create($data);
            $this->updateQuietly(['model_id' => $donation->id]);
        } elseif ($this->action === 'update') {
            Donation::findOrFail($this->model_id)->update($data);
        }
    }

    private function applyMemberImage(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            $img = MemberImage::find($this->model_id);
            if ($img) {
                Storage::disk('public')->delete($img->file_path);
                $img->delete();
            }
            return;
        }

        if ($this->action === 'create') {
            $img = MemberImage::create([
                'member_id'   => $p['member_id'],
                'title'       => $p['title']       ?? null,
                'file_path'   => $p['file_path'],
                'file_name'   => $p['file_name'],
                'file_size'   => $p['file_size']   ?? null,
                'mime_type'   => $p['mime_type']   ?? null,
                'uploaded_by' => $p['uploaded_by'] ?? Auth::id(),
            ]);
            $this->updateQuietly(['model_id' => $img->id]);
        } elseif ($this->action === 'update') {
            MemberImage::findOrFail($this->model_id)->update(['title' => $p['title'] ?? null]);
        }
    }

    /** Arabic labels for diff display */
    public static function memberFieldLabels(): array
    {
        return [
            'full_name'                 => 'الاسم الكامل',
            'age'                       => 'العمر',
            'gender'                    => 'الجنس',
            'mother_name'               => 'اسم الأم',
            'national_id'               => 'رقم الهوية',
            'verification_status_id'    => 'حالة التحقق',
            'dossier_number'            => 'رقم الاضبارة',
            'current_address'           => 'العنوان التفصيلي',
            'marital_status'            => 'الحالة الاجتماعية',
            'disease_type'              => 'نوع المرض',
            'phone'                     => 'الهاتف',
            'phone2'                    => 'الهاتف الثاني',
            'network'                   => 'نوع الشبكة',
            'provider_status'           => 'حالة المعيل',
            'job'                       => 'العمل',
            'housing_status_id'         => 'وضع السكن',
            'region_id'                 => 'المنطقة',
            'sector_id'                 => 'القطاع',
            'final_status_id'           => 'الحالة النهائية',
            'dependents_count'          => 'عدد المعالين',
            'payments_count'            => 'عدد الدفعات',
            'notes'                     => 'ملاحظة',
            'illness_details'           => 'تفاصيل المرض',
            'special_cases'             => 'حالة خاصة',
            'special_cases_description' => 'وصف الحالة الخاصة',
            'sham_cash_account'         => 'حساب شام كاش',
            'other_association'         => 'جمعية أخرى',
            'representative_id'         => 'الممثل المسؤول',
            'data_entry_name'           => 'اسم المدخل (يدوي)',
            'delegate'                  => 'المندوب الخارجي',
            'second_person'             => 'الشخص الثاني',
            'association_id'            => 'الجمعية',
            'score'                     => 'مجموع النقاط',
            'estimated_amount'          => 'المبلغ المقدر',
            'scores.work_score'             => 'نقاط العمل',
            'scores.housing_score'          => 'نقاط السكن',
            'scores.dependents_score'       => 'نقاط عدد الأفراد',
            'scores.dependent_status_score' => 'نقاط حالة المعيل',
            'scores.illness_score'          => 'نقاط المرض',
            'scores.special_cases_score'    => 'نقاط الحالات الخاصة',
            'payment.iban'                  => 'رقم الآيبان',
            'payment.barcode'               => 'الباركود',
            'payment.recipient_name'        => 'اسم المستلم',
            'payment.data_entry_name'       => 'اسم مدخل بيانات الدفع',
            'estimated_amount'              => 'المبلغ المقدر',
            'field_visit_status_id'         => 'حالة الجولة الميدانية',
            'fv_visitor'                    => 'زائر الجولة',
            'payment_data_entry_name'       => 'اسم مدخل الدفع',
            'payment_ai.iban'               => 'رقم الآيبان AI',
            'payment_ai.barcode'            => 'الباركود AI',
            'payment_ai.recipient_name'     => 'اسم المستلم AI',
        ];
    }

    public static function donationFieldLabels(): array
    {
        return [
            'member_name'      => 'العضو',
            'amount'           => 'المبلغ (ل.س)',
            'donation_month'   => 'شهر التبرع',
            'type'             => 'النوع',
            'status'           => 'الحالة',
            'reference_number' => 'رقم المرجع',
            'notes'            => 'ملاحظات',
        ];
    }

    public static function memberImageFieldLabels(): array
    {
        return [
            'member_name' => 'العضو',
            'title'       => 'العنوان / الوصف',
            'file_name'   => 'اسم الملف',
            'mime_type'   => 'نوع الملف',
            'file_size'   => 'الحجم',
        ];
    }

    private function applyMaritalStatus(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            MaritalStatus::find($this->model_id)?->delete();
            return;
        }

        $data = ['name' => $p['name'], 'is_active' => $p['is_active'] ?? 1];

        if ($this->action === 'create') {
            MaritalStatus::create($data);
        } elseif ($this->action === 'update') {
            MaritalStatus::findOrFail($this->model_id)->update($data);
        }
    }

    private function applyAssociation(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            Association::find($this->model_id)?->delete();
            return;
        }

        $data = ['name' => $p['name'], 'is_active' => $p['is_active'] ?? true];

        if ($this->action === 'create') {
            Association::create($data);
        } elseif ($this->action === 'update') {
            Association::findOrFail($this->model_id)->update($data);
        }
    }

    private function applyVerificationStatus(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            VerificationStatus::find($this->model_id)?->delete();
            return;
        }

        $data = ['name' => $p['name'], 'color' => $p['color'] ?? 'gray', 'is_active' => $p['is_active'] ?? true];

        if ($this->action === 'create') {
            VerificationStatus::create($data);
        } elseif ($this->action === 'update') {
            VerificationStatus::findOrFail($this->model_id)->update($data);
        }
    }

    public static function maritalStatusFieldLabels(): array
    {
        return [
            'name'      => 'الاسم',
            'is_active' => 'نشط',
        ];
    }

    public static function associationFieldLabels(): array
    {
        return [
            'name'      => 'الاسم',
            'is_active' => 'نشط',
        ];
    }

    public static function verificationStatusFieldLabels(): array
    {
        return [
            'name'      => 'الاسم',
            'color'     => 'اللون',
            'is_active' => 'نشط',
        ];
    }

    public static function fieldVisitFieldLabels(): array
    {
        return [
            'member_name'           => 'المستفيد',
            'field_visit_status_id' => 'حالة الجولة',
            'house_type_id'         => 'نوع البيت',
            'house_condition_id'    => 'حالة البيت',
            'visit_date'            => 'تاريخ الزيارة',
            'visitor'               => 'اسم الزائر',
            'estimated_amount'      => 'المبلغ المقدر (ل.س)',
            'amount_reason'         => 'سبب المبلغ',
            'notes'                 => 'ملاحظات',
            'has_video'             => 'يوجد فيديو',
            'has_special_case'      => 'حالة خاصة',
        ];
    }

    private function applyRegion(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            \App\Models\Region::find($this->model_id)?->delete();
            return;
        }

        $data = ['name' => $p['name'], 'is_active' => $p['is_active'] ?? true];

        if ($this->action === 'create') {
            $region = \App\Models\Region::create($data);
            $this->updateQuietly(['model_id' => $region->id]);
        } elseif ($this->action === 'update') {
            \App\Models\Region::findOrFail($this->model_id)->update($data);
        }
    }

    public static function regionFieldLabels(): array
    {
        return [
            'name'      => 'الاسم',
            'is_active' => 'نشط',
        ];
    }

    private function applyFieldVisit(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            \App\Models\FieldVisit::find($this->model_id)?->delete();
            return;
        }

        $data = [
            'field_visit_status_id' => $p['field_visit_status_id'] ?? null,
            'house_type_id'         => $p['house_type_id']         ?? null,
            'house_condition_id'    => $p['house_condition_id']    ?? null,
            'visit_date'            => $p['visit_date']            ?? null,
            'visitor'               => $p['visitor']               ?? null,
            'estimated_amount'      => $p['estimated_amount']      ?? null,
            'amount_reason'         => $p['amount_reason']         ?? null,
            'notes'                 => $p['notes']                 ?? null,
            'has_video'             => (bool) ($p['has_video']          ?? false),
            'has_special_case'      => (bool) ($p['has_special_case']   ?? false),
        ];

        $member = \App\Models\Member::find($p['member_id'] ?? null);

        if ($this->action === 'create') {
            if ($member) {
                $data['created_by'] = $this->requested_by;
                $visit = $member->fieldVisits()->create($data);
                $this->updateQuietly(['model_id' => $visit->id]);
            }
        } elseif ($this->action === 'update') {
            $visit = \App\Models\FieldVisit::find($this->model_id);
            if ($visit) {
                $visit->update($data);
            }
        }
    }
}

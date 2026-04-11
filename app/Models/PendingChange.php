<?php

namespace App\Models;

use App\Models\Association;
use App\Models\Donation;
use App\Models\MaritalStatus;
use App\Models\MemberImage;
use App\Models\PaymentInfoAI;
use App\Models\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
            'bulk_amount'  => 'تعديل جماعي للمبلغ',
            'bulk_delete'  => 'حذف جماعي',
            'bulk_update'  => 'تعديل جماعي',
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
            default               => $this->model_type,
        };
    }

    /** A short description for list views */
    public function summary(): string
    {
        if ($this->action === 'bulk_amount') {
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
            'member'              => $this->payload['full_name']   ?? $this->original['full_name']   ?? "#{$this->model_id}",
            'donation'            => $this->payload['member_name'] ?? $this->original['member_name'] ?? "#{$this->model_id}",
            'member_image'        => $this->payload['member_name'] ?? $this->original['member_name'] ?? "#{$this->model_id}",
            'field_visit'         => $this->payload['member_name'] ?? $this->original['member_name'] ?? "#{$this->model_id}",
            'marital_status',
            'association',
            'verification_status' => $this->payload['name'] ?? $this->original['name'] ?? "#{$this->model_id}",
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
        $field     = in_array($p['field'] ?? '', ['estimated_amount', 'final_amount'])
                        ? $p['field']
                        : 'estimated_amount';

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
        $totalScore           = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;

        $memberData = [
            'full_name'                 => $p['full_name']                 ?? null,
            'age'                       => $p['age']                       ?? null,
            'gender'                    => $p['gender']                    ?? null,
            'mother_name'               => $p['mother_name']               ?? null,
            'national_id'               => $p['national_id']               ?? null,
            'verification_status_id'    => $p['verification_status_id']    ?? null,
            'dossier_number'            => $p['dossier_number']            ?? null,
            'current_address'           => $p['current_address']           ?? null,
            'marital_status'            => $p['marital_status']            ?? null,
            'disease_type'              => $p['disease_type']              ?? null,
            'other_association'         => $p['other_association']         ?? false,
            'phone'                     => $p['phone']                     ?? null,
            'representative_id'         => $p['representative_id']         ?? null,
            'delegate'                  => $p['delegate']                  ?? null,
            'association_id'            => $p['association_id']            ?? null,
            'network'                   => $p['network']                   ?? null,
            'provider_status'           => $p['provider_status']           ?? null,
            'job'                       => $p['job']                       ?? null,
            'housing_status'            => $p['housing_status']            ?? null,
            'dependents_count'          => $p['dependents_count']          ?? null,
            'illness_details'           => $p['illness_details']           ?? null,
            'special_cases'             => $p['special_cases']             ?? false,
            'special_cases_description' => $p['special_cases_description'] ?? null,
            'sham_cash_account'         => in_array($p['sham_cash_account'] ?? '', ['done','manual']) ? $p['sham_cash_account'] : null,
            'score'                     => $totalScore,
            'estimated_amount'          => $totalScore * 500,
        ];

        $scoresData = [
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
        ];

        $payment   = $p['payment']    ?? [];
        $paymentAI = $p['payment_ai'] ?? [];

        if ($this->action === 'create') {
            $memberData['final_amount'] = $totalScore * 500;
            $member = Member::create($memberData);
            MemberScore::create(array_merge($scoresData, ['member_id' => $member->id]));
            PaymentInfo::create(array_merge([
                'member_id'      => $member->id,
                'iban'           => $payment['iban']           ?? null,
                'barcode'        => $payment['barcode']        ?? null,
                'iban_image'     => $payment['iban_image']     ?? null,
                'barcode_image'  => $payment['barcode_image']  ?? null,
                'recipient_name' => $payment['recipient_name'] ?? null,
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
            $visitAmount = \App\Models\FieldVisit::where('member_id', $this->model_id)->latest()->value('estimated_amount') ?? 0;
            $memberData['final_amount'] = $totalScore * 500 + $visitAmount;
            $member->update($memberData);

            $s = $member->scores ?? new MemberScore(['member_id' => $member->id]);
            $s->fill(array_merge($scoresData, ['member_id' => $member->id]))->save();

            $pay = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);
            $pay->fill([
                'member_id'      => $member->id,
                'iban'           => $payment['iban']           ?? $pay->iban,
                'barcode'        => $payment['barcode']        ?? $pay->barcode,
                'iban_image'     => $payment['iban_image']     ?? $pay->iban_image,
                'barcode_image'  => $payment['barcode_image']  ?? $pay->barcode_image,
                'recipient_name' => $payment['recipient_name'] ?? $pay->recipient_name,
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
            Donation::create($data);
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
            MemberImage::create([
                'member_id'   => $p['member_id'],
                'title'       => $p['title']       ?? null,
                'file_path'   => $p['file_path'],
                'file_name'   => $p['file_name'],
                'file_size'   => $p['file_size']   ?? null,
                'mime_type'   => $p['mime_type']   ?? null,
                'uploaded_by' => $p['uploaded_by'] ?? Auth::id(),
            ]);
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
            'network'                   => 'نوع الشبكة',
            'provider_status'           => 'حالة المعيل',
            'job'                       => 'العمل',
            'housing_status'            => 'حالة السكن',
            'dependents_count'          => 'عدد المعالين',
            'illness_details'           => 'تفاصيل المرض',
            'special_cases'             => 'حالة خاصة',
            'special_cases_description' => 'وصف الحالة الخاصة',
            'sham_cash_account'         => 'حساب شام كاش',
            'other_association'         => 'جمعية أخرى',
            'representative_id'         => 'الممثل المسؤول',
            'delegate'                  => 'المندوب الخارجي',
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
            'estimated_amount'              => 'المبلغ المقدر',
            'final_amount'                  => 'المبلغ النهائي',
            'field_visit_status_id'         => 'حالة الجولة الميدانية',
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
            'visit_date'            => 'تاريخ الزيارة',
            'visitor'               => 'اسم الزائر',
            'estimated_amount'      => 'المبلغ المقدر (ل.س)',
            'amount_reason'         => 'سبب المبلغ',
            'notes'                 => 'ملاحظات',
        ];
    }

    private function applyFieldVisit(): void
    {
        $p = $this->payload ?? [];

        if ($this->action === 'delete') {
            \App\Models\FieldVisit::find($this->model_id)?->delete();
            $member = \App\Models\Member::find($p['member_id'] ?? null);
            if ($member) {
                $visitAmount = $member->fieldVisits()->latest()->value('estimated_amount') ?? 0;
                $member->update(['final_amount' => ($member->estimated_amount ?? 0) + $visitAmount]);
            }
            return;
        }

        $data = [
            'field_visit_status_id' => $p['field_visit_status_id'] ?? null,
            'visit_date'            => $p['visit_date']            ?? null,
            'visitor'               => $p['visitor']               ?? null,
            'estimated_amount'      => $p['estimated_amount']      ?? null,
            'amount_reason'         => $p['amount_reason']         ?? null,
            'notes'                 => $p['notes']                 ?? null,
        ];

        $member = \App\Models\Member::find($p['member_id'] ?? null);

        if ($this->action === 'create') {
            if ($member) {
                $member->fieldVisits()->create($data);
                $visitAmount = $member->fieldVisits()->latest()->value('estimated_amount') ?? 0;
                $member->update(['final_amount' => ($member->estimated_amount ?? 0) + $visitAmount]);
            }
        } elseif ($this->action === 'update') {
            $visit = \App\Models\FieldVisit::find($this->model_id);
            if ($visit) {
                $visit->update($data);
                if ($member) {
                    $visitAmount = $member->fieldVisits()->latest()->value('estimated_amount') ?? 0;
                    $member->update(['final_amount' => ($member->estimated_amount ?? 0) + $visitAmount]);
                }
            }
        }
    }
}

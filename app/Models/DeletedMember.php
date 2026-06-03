<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedMember extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'original_id', 'full_name', 'dossier_number',
        'data', 'deleted_by', 'deleted_by_name',
    ];

    protected $casts = [
        'data'       => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Snapshot a member's full data and save it before deletion.
     */
    public static function archive(Member $member, ?int $userId = null): self
    {
        $member->loadMissing([
            'scores', 'paymentInfo', 'paymentInfoAI', 'paymentReview',
            'associations', 'images', 'fieldVisits',
            'verificationStatus', 'finalStatus', 'housingStatus',
            'association', 'region', 'sector', 'representative',
        ]);

        $deleterName = $userId ? User::find($userId)?->name : null;

        return static::create([
            'original_id'     => $member->id,
            'full_name'       => $member->full_name,
            'dossier_number'  => $member->dossier_number,
            'deleted_by'      => $userId,
            'deleted_by_name' => $deleterName,
            'data'            => static::buildSnapshot($member),
        ]);
    }

    private static function buildSnapshot(Member $member): array
    {
        return [
            'member' => $member->getAttributes(),

            // Related labels (for readability in archive)
            'verification_status' => $member->verificationStatus?->only(['id', 'name', 'color']),
            'final_status'        => $member->finalStatus?->only(['id', 'name', 'color']),
            'housing_status'      => $member->housingStatus?->only(['id', 'name']),
            'association'         => $member->association?->only(['id', 'name']),
            'region'              => $member->region?->only(['id', 'name']),
            'sector'              => $member->sector?->only(['id', 'name']),
            'representative'      => $member->representative?->only(['id', 'name']),

            // Scores
            'scores' => $member->scores?->getAttributes(),

            // Payment data
            'payment_info'    => $member->paymentInfo?->getAttributes(),
            'payment_info_ai' => $member->paymentInfoAI?->getAttributes(),
            'payment_review'  => $member->paymentReview?->getAttributes(),

            // Many-to-many associations
            'associations' => $member->associations
                ->map->only(['id', 'name'])
                ->values()
                ->all(),

            // Uploaded images (paths kept for reference)
            'images' => $member->images
                ->map(fn($i) => $i->only(['id', 'file_path', 'file_name', 'title', 'mime_type', 'file_size']))
                ->values()
                ->all(),

            // Field visits
            'field_visits' => $member->fieldVisits
                ->map->getAttributes()
                ->values()
                ->all(),
        ];
    }
}

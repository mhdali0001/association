<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'full_name', 'age', 'gender', 'mother_name', 'national_id',
        'verification_status_id', 'final_status_id', 'dossier_number', 'current_address', 'region_id',
        'marital_status', 'disease_type', 'other_association', 'phone',
        'representative_id', 'delegate', 'network', 'provider_status', 'job',
        'housing_status', 'dependents_count', 'illness_details',
        'special_cases', 'special_cases_description', 'score',
        'estimated_amount', 'final_amount', 'sham_cash_account', 'association_id',
    ];

    protected $casts = [
        'other_association' => 'boolean',
        'special_cases'     => 'boolean',
        // sham_cash_account is enum('done','manual') nullable — no cast needed
        'estimated_amount'  => 'decimal:2',
        'final_amount'      => 'decimal:2',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function representative()
    {
        return $this->belongsTo(User::class, 'representative_id');
    }

    public function verificationStatus()
    {
        return $this->belongsTo(VerificationStatus::class, 'verification_status_id');
    }

    public function finalStatus()
    {
        return $this->belongsTo(FinalStatus::class, 'final_status_id');
    }

    public function scores()
    {
        return $this->hasOne(MemberScore::class);
    }

    public function paymentInfo()
    {
        return $this->hasOne(PaymentInfo::class);
    }

    public function paymentInfoAI()
    {
        return $this->hasOne(PaymentInfoAI::class);
    }

    public function paymentReview()
    {
        return $this->hasOne(PaymentReview::class);
    }

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function associations()
    {
        return $this->belongsToMany(Association::class, 'member_associations');
    }

    public function images()
    {
        return $this->hasMany(MemberImage::class)->latest();
    }

    public function fieldVisits()
    {
        return $this->hasMany(\App\Models\FieldVisit::class)->latest();
    }
}

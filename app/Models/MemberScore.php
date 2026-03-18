<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberScore extends Model
{
    protected $table = 'member_scores';
    protected $fillable = [
        'member_id', 'work_score', 'housing_score',
        'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score', 'total_score',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

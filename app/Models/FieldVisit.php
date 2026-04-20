<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisit extends Model
{
    protected $fillable = ['member_id', 'field_visit_status_id', 'house_type_id', 'house_condition_id', 'visit_date', 'visitor', 'estimated_amount', 'amount_reason', 'notes', 'has_video'];

    protected $casts = ['visit_date' => 'date', 'has_video' => 'boolean'];

    public function member() { return $this->belongsTo(Member::class); }
    public function status() { return $this->belongsTo(FieldVisitStatus::class, 'field_visit_status_id'); }
    public function houseType() { return $this->belongsTo(HouseType::class); }
    public function houseCondition() { return $this->belongsTo(HouseCondition::class); }
}

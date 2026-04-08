<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisit extends Model
{
    protected $fillable = ['member_id', 'field_visit_status_id', 'visit_date', 'visitor', 'estimated_amount', 'amount_reason', 'notes'];

    protected $casts = ['visit_date' => 'date'];

    public function member() { return $this->belongsTo(Member::class); }
    public function status() { return $this->belongsTo(FieldVisitStatus::class, 'field_visit_status_id'); }
}

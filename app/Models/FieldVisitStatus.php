<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisitStatus extends Model
{
    protected $fillable = ['name', 'color', 'is_active'];

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function fieldVisits() { return $this->hasMany(FieldVisit::class); }
}

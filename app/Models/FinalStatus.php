<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalStatus extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'color', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'final_status_id');
    }
}

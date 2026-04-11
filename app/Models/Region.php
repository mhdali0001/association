<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

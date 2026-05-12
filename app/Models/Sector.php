<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

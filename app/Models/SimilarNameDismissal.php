<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimilarNameDismissal extends Model
{
    protected $fillable = ['member_a_id', 'member_b_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** مفتاح الزوج بغض النظر عن الترتيب */
    public static function key(int $a, int $b): string
    {
        return min($a, $b) . '-' . max($a, $b);
    }
}

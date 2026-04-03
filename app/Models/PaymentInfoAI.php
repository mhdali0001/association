<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInfoAI extends Model
{
    protected $table = 'payment_info_AI';
    protected $fillable = ['member_id', 'iban', 'barcode'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

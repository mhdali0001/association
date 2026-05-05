<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    protected $table = 'payment_info';
    protected $fillable = ['member_id', 'iban', 'barcode', 'iban_image', 'barcode_image', 'recipient_name', 'data_entry_name'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

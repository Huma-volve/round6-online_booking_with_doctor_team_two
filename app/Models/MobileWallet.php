<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileWallet extends Model
{
    //
    use HasFactory;
    protected $fillable=[
        'patient_id',
        'Type',
         'stripe_payment_method_id',
         'last4'

    ];
        public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

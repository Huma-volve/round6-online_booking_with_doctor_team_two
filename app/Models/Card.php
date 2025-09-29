<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
        use HasFactory;

    //
    protected $fillable=[
        'patient_id',
        'CardHolderName',
        'last4',
        'Type',
        'token'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

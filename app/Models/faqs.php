<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class faqs extends Model
{
    //
    protected $fillable = [
        'question', 'answer', 'order','status'
    ];
}

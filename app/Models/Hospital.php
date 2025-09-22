<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'start_time', 'end_time', 'address', 'image', 'rate'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_hospital');
    }
}

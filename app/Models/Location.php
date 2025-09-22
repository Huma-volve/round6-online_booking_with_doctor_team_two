<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['city', 'street', 'building_no'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_location');
    }
}

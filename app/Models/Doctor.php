<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'major_id',
        'price',
        'rating',
        'experience',
        'bio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favourites()
    {
        return $this->belongsToMany(Patient::class, 'favourites');
    }

    public function hospitals()
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'doctor_location');
    }
}

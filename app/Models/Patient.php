<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'record'];

    public function user()
    {
        return $this->belongsTo(User::class);
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
        return $this->belongsToMany(Doctor::class, 'favourites');
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }
    public function cards()
{
    return $this->hasMany(Card::class);
}

public function mobileWallets()
{
    return $this->hasMany(MobileWallet::class);
}

}

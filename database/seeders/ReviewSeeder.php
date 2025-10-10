<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Doctor::count() < 5) {
            User::factory()->count(5)->state(['role' => 'doctor'])->create()->each(function ($user) {
                \App\Models\Doctor::factory()->create([
                    'user_id' => $user->id,
                ]);
            });
        }

        if (Patient::count() < 10) {
            User::factory()->count(10)->state(['role' => 'patient'])->create()->each(function ($user) {
                \App\Models\Patient::factory()->create([
                    'user_id' => $user->id,
                ]);
            });
        }

        Review::factory()->count(40)->create();

        Doctor::all()->each(function ($doctor) {
            $avg = $doctor->reviews()->avg('rating');
            $doctor->rating = $avg ? round($avg, 2) : 0;
            $doctor->save();
        });
    }
}

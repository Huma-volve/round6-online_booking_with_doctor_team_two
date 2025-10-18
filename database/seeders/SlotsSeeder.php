<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Slot;

class SlotsSeeder extends Seeder
{
    public function run()
    {
        $doctors = Doctor::all();

        if ($doctors->isEmpty()) {
            $doctors = Doctor::factory()->count(3)->create();
        }

        foreach ($doctors as $doctor) {
            if ($doctor->slots()->count() === 0) {
                Slot::factory()->count(8)->create([
                    'doctor_id' => $doctor->id,
                ]);
            }
        }

    }
}

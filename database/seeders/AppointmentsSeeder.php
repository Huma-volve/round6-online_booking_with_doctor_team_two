<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Slot;
use Carbon\Carbon;

class AppointmentsSeeder extends Seeder
{
    public function run()
    {
        $doctors = Doctor::all();
        if ($doctors->isEmpty()) {
            $doctors = Doctor::factory()->count(3)->create();
        }

        $patients = Patient::all();
        if ($patients->isEmpty()) {
            $patients = Patient::factory()->count(6)->create();
        }

        // Ensure slots exist for doctors
        foreach ($doctors as $doctor) {
            if ($doctor->slots()->count() === 0) {
                Slot::factory()->count(8)->create(['doctor_id' => $doctor->id]);
            }
        }

        // 1) Create a deterministic upcoming appointment (2 hours from now) -> confirmed
        $doctor = $doctors->random();
        $patient = $patients->random();
        $slot = Slot::where('doctor_id', $doctor->id)->inRandomOrder()->first();

        $startsAt = Carbon::now()->addHours(2);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
            'date' => $startsAt->toDateString(),
            'time' => $startsAt->format('H:i:s'),
            'status' => 'confirmed',
            'price' => 50.00,
        ]);

        // 2) A canceled appointment in the past
        $doctor = $doctors->random();
        $patient = $patients->random();
        $slot = Slot::where('doctor_id', $doctor->id)->inRandomOrder()->first();
        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'slot_id' => $slot->id,
            'date' => Carbon::now()->subDays(1)->toDateString(),
            'time' => Carbon::now()->subDays(1)->format('H:i:s'),
            'status' => 'canceled',
            'price' => 40.00,
        ]);

        // 3) Completed/other historic appointments - create via factory for variety
        Appointment::factory()->count(8)->create();

    }
}

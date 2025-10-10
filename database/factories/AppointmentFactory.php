<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Slot;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition()
    {
        $startsAt = $this->faker->dateTimeBetween('-3 days', '+7 days');

        $doctor = Doctor::factory();

        return [
            'doctor_id' => $doctor,
            'slot_id' => function (array $attributes) use ($doctor, $startsAt) {
                $doctorId = $attributes['doctor_id'] ?? null;
                if (!$doctorId) {
                    $doctorModel = $doctor->create();
                    $doctorId = $doctorModel->id;
                }
                return Slot::factory()->create([
                    'doctor_id' => $doctorId,
                    'day_of_week' => Carbon::parse($startsAt)->format('l'),
                ])->id;
            },

            'patient_id' => Patient::factory(),
            'date' => $startsAt->format('Y-m-d'),
            'time' => $startsAt->format('H:i:s'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled']),
            'price' => $this->faker->randomFloat(2, 10, 300),
        ];
    }

    public function status(string $status)
    {
        return $this->state(fn() => ['status' => $status]);
    }
}

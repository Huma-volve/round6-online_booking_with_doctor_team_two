<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $doctor = Doctor::inRandomOrder()->first();
        $patient = Patient::inRandomOrder()->first();

        // If none exist, create minimal placeholder (so factory is safe)
        if (!$doctor) {
            $doctor = Doctor::factory()->create();
        }
        if (!$patient) {
            $patient = Patient::factory()->create();
        }

        return [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(12),
            'is_verified' => $this->faker->boolean(50),
        ];
    }
}

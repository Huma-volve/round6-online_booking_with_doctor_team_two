<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $major = Major::inRandomOrder()->first();
        if (!$major) {
            $major = Major::factory()->create(); // if you have MajorFactory
        }

        return [
            'user_id' => User::factory()->state(['role' => 'doctor']),
            'major_id' => $major->id,
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'rating' => 0,
            'experience' => $this->faker->numberBetween(1, 40),
            'bio' => $this->faker->sentence(12),
        ];
    }
}

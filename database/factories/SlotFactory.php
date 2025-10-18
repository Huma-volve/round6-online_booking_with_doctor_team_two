<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slot>
 */
class SlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(8, 16);
        $startMin = $this->faker->randomElement([0, 15, 30, 45]);
        $start = sprintf('%02d:%02d:00', $startHour, $startMin);

        $endTime = Carbon::createFromFormat('H:i:s', $start)->addMinutes(30)->format('H:i:s');

        return [
            'doctor_id' => Doctor::factory(),
            'start_time' => $start,
            'end_time' => $endTime,
            'day_of_week' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'status' => $this->faker->randomElement(['not_yet', 'reserved']),
        ];
    }
}

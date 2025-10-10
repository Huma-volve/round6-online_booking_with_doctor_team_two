<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['upcoming', 'canceled', 'completed', 'booked']);

        return [
            'user_id' => User::factory(),
            'title' => ucfirst($type) . ' appointment',
            'message' => $this->faker->sentence(8),
            'type' => $type,
            'is_read' => $this->faker->boolean(40),
        ];
    }
}

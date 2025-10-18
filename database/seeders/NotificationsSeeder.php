<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Appointment;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }

        $users = User::take(8)->get();

        foreach ($users as $user) {
            Notification::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }

        // Additionally create notifications tied to appointments (unread)
        $appointment = Appointment::inRandomOrder()->first();
        if ($appointment) {
            if ($appointment->patient && $appointment->patient->user) {
                Notification::create([
                    'user_id' => $appointment->patient->user->id,
                    'title' => 'Upcoming Appointment',
                    'message' => "Reminder: you have an appointment on {$appointment->date} at {$appointment->time}",
                    'type' => 'upcoming',
                    'is_read' => false,
                ]);
            }

            if ($appointment->doctor && $appointment->doctor->user) {
                Notification::create([
                    'user_id' => $appointment->doctor->user->id,
                    'title' => 'Upcoming Appointment',
                    'message' => "Reminder: you have an appointment on {$appointment->date} at {$appointment->time}",
                    'type' => 'upcoming',
                    'is_read' => false,
                ]);
            }
        }

    }
}

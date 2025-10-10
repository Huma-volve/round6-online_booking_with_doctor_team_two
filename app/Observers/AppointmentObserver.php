<?php

namespace App\Observers;

use App\Jobs\CreateDatabaseNotification;
use App\Models\Appointment;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $patientUser = $appointment->patient->user;
        $doctorUser = $appointment->doctor->user;

        $data = [
            'appointment_id' => $appointment->id,
            'appointment_time' => $appointment->starts_at?->toDateTimeString(),
            'status' => $appointment->status,
        ];

        if ($patientUser){
            CreateDatabaseNotification::dispatch(
                $patientUser,
                'Appointment booked',
                "You have successfully booked an appoinment with Dr. {$appointment->doctor->user->full_name} on {$appointment->starts_at?->toDayDateTimeString()}",
                'booked',
                $data
            );
        }

        if ($doctorUser){
            CreateDatabaseNotification::dispatch(
                $doctorUser,
                'New Appointment',
                "You have a new appointment booked by {$appointment->patient->user->full_name} on {$appointment->starts_at?->toDayDateTimeString()}",
                'booked',
                $data
            );
        }
        $reminderAt = $appointment->starts_at->copy()->subHour();
        if ($reminderAt->isFuture()) {
            if ($patientUser) {
                CreateDatabaseNotification::dispatch(
                    $patientUser,
                    'Upcoming Appointment',
                    "Reminder: You have an appoinment with Dr. {$appointment->doctor->user->name} at {$appointment->starts_at?->format('H:i, d M Y')}",
                    'upcoming',
                    $data
                )->delay($reminderAt);
            }
            if ($doctorUser) {
                CreateDatabaseNotification::dispatch(
                    $doctorUser,
                    'Upcoming Appointment',
                    "Reminder: You have an appoinment with {$appointment->patient->user->name} at {$appointment->starts_at?->format('H:i, d M Y')}",
                    'upcoming',
                    $data
                )->delay($reminderAt);
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $original = $appointment->status->getOriginal('status');
        $current = $appointment->status;
        if ($original === $current){
            return;
        }

        $patientUser = $appointment->patient->user;
        $doctorUser = $appointment->doctor->user;

        $data = [
            'appointment_id' => $appointment->id,
            'appointment_time' => $appointment->starts_at?->toDateTimeString(),
            'status' => $appointment->status,
        ];

        if ($current === 'canceled'){
            if($patientUser){
                CreateDatabaseNotification::dispatch(
                    $patientUser,
                    "Your appointment on {$appointment->starts_at?->toDayDateTimeString()} was cancelled",
                    'canceled',
                    $data,
                );
            }
            if ($doctorUser) {
                CreateDatabaseNotification::dispatch(
                    $doctorUser,
                    "An appointment on {$appointment->starts_at?->toDayDateTimeString()} was cancelled",
                    'canceled',
                    $data,
                );
            }
        }
        elseif($current === 'completed'){
            if ($patientUser) {
                CreateDatabaseNotification::dispatch(
                    $patientUser,
                    "Your appointment on {$appointment->starts_at?->toDayDateTimeString()} was marked complete",
                    'completed',
                    $data,
                );
            }
            if ($doctorUser) {
                CreateDatabaseNotification::dispatch(
                    $doctorUser,
                    "Your appointment on {$appointment->starts_at?->toDayDateTimeString()} was marked complete",
                    'completed',
                    $data,
                );
            }
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}

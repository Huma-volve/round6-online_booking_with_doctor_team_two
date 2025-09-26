<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    public static function send($to, $message)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        $client = new Client($sid, $token);
        return $client->messages->create($to, [
            'from' => $from,
            'body' => $message,
        ]);
    }
}

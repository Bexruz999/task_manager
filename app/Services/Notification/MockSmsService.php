<?php

namespace App\Services\Notification;

use App\Contracts\SmsNotificationInterface;
use Log;

class MockSmsService implements SmsNotificationInterface
{
    public function send(string $phone, string $message): bool
    {
        Log::channel('sms')->info("SMS to $phone: $message");
        return true;
    }
}

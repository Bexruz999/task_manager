<?php

namespace App\Contracts;

interface SmsNotificationInterface {
    public function send(string $phone, string $message): bool;
}

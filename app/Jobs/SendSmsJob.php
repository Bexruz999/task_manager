<?php

namespace App\Jobs;

use App\Contracts\SmsNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $phone,
        protected string $message
    ) {}

    public function handle(SmsNotificationInterface $smsService): void
    {
        $smsService->send($this->phone, $this->message);
    }
}

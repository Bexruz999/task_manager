<?php

namespace App\Providers;

use App\Contracts\GeocoderInterface;
use App\Contracts\PaymentInterface;
use App\Contracts\RoutingInterface;
use App\Contracts\SmsNotificationInterface;
use App\Services\Maps\MockGeocoderService;
use App\Services\Maps\MockRoutingService;
use App\Services\Notification\MockSmsService;
use App\Services\Payment\MockPayment;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeocoderInterface::class, MockGeocoderService::class);
        $this->app->bind(RoutingInterface::class, MockRoutingService::class);
        $this->app->bind(SmsNotificationInterface::class, MockSmsService::class);
        $this->app->bind(PaymentInterface::class, MockPayment::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

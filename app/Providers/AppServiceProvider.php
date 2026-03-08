<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Event::listen(
            \App\Events\PoojaBooked::class,
            \App\Listeners\SendPoojaBookingNotification::class,
        );
        
        \Event::listen(
            \App\Events\ContactFormSubmitted::class,
            \App\Listeners\SendContactNotification::class,
        );
        
        \Event::listen(
            \App\Events\QuestionSubmitted::class,
            \App\Listeners\SendQuestionNotifications::class,
        );
    }
}

<?php

namespace App\Providers;

use App\Listeners\SendQueuedMailableListener;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
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
        // Register mail event listeners
        \Event::listen(MessageSending::class, [SendQueuedMailableListener::class, 'handleSending']);
        \Event::listen(MessageSent::class, [SendQueuedMailableListener::class, 'handleSent']);
    }
}

<?php

namespace Newton\InvestorTesting\Providers;

use Newton\InvestorTesting\Events\ExampleEvent;
use Newton\InvestorTesting\Listeners\ExampleListener;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ExampleEvent::class => [
            ExampleListener::class,
        ],
    ];
}

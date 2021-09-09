<?php

namespace Plugins\Example\ExampleLetter;

use Newton\InvestorTesting\Packages\Common\UserRepository;
use Plugins\Plugin;

class ExampleLetterPlugin extends Plugin
{
    public function register()
    {
        $this->app->configure('view');
        config()->set('view.paths', array_merge(config('view.paths'), [__DIR__ . '/resources/views']));
    }

    public function boot()
    {
        $this->app->singleton(UserRepository::class, function () {
            return new \Plugins\Example\ExampleLetter\App\Packages\Common\UserRepository();
        });
    }
}

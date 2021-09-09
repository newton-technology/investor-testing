<?php

namespace Plugins\Example\ExampleView;

use Common\Packages\Application\Application;
use Newton\InvestorTesting\Packages\Authorization\CodeRepository;
use Plugins\Plugin;

class ExampleViewPlugin extends Plugin
{
    public function register()
    {
        $this->app->configure('view');
        config()->set('view.paths', array_merge(config('view.paths'), [__DIR__ . '/resources/views']));
    }

    public function boot()
    {
        $this->app->singleton(CodeRepository::class, function (Application $application) {
            return $application->make(\Plugins\Example\ExampleView\App\Packages\Common\CodeRepository::class);
        });
    }
}

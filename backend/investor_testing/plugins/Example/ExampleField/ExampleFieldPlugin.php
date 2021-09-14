<?php

namespace Plugins\Example\ExampleField;

use Common\Packages\Application\Application;
use Common\Plugins\Plugin;
use Newton\InvestorTesting\Packages\Common\CategoryItemRepository;

class ExampleFieldPlugin extends Plugin
{
    public function boot()
    {
        $this->app->singleton(CategoryItemRepository::class, function (Application $application) {
            return $application->make(\Plugins\Example\ExampleField\App\Packages\Common\CategoryItemRepository::class);
        });
    }
}

<?php

declare(strict_types=1);

namespace Plugins\Example\ExampleTestResultView;

use Common\Packages\Application\Application;
use Newton\InvestorTesting\Packages\Authorization\CodeRepository;
use Plugins\Plugin;

class ExampleTestResultViewPlugin extends Plugin
{
    public function register()
    {
        $this->app->configure('view');
        config()->set('view.paths', array_merge(config('view.paths'), [__DIR__ . '/resources/views']));
    }
}

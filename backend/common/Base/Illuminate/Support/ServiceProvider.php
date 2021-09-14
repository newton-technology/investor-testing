<?php

namespace Common\Base\Illuminate\Support;

abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Объединяет рекурсивно переданную конфигурацию с существующей
     *
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key): void
    {
        $config = $this->app['config']->get($key, []);
        $this->app['config']->set($key, array_replace_recursive(require $path, $config));
    }
}

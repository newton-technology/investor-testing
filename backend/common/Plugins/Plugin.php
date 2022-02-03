<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace Common\Plugins;

use Common\Packages\Application\Application;
use Common\Base\Illuminate\Support\ServiceProvider;

abstract class Plugin extends ServiceProvider
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

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
        $configToMerge = require $path;
        if ($key === 'authorization') {
            foreach ($configToMerge['issuer'] ?? [] as $service => $parameters) {
                $configToMerge['issuer'][$service]['aud'] = array_merge(
                    $config['issuer'][$service]['aud'] ?? [],
                    $parameters['aud'] ?? []
                );
            }
        }
        $this->app['config']->set($key, array_replace_recursive($configToMerge, $config));
    }
}

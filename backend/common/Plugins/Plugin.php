<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace Common\Plugins;

use Common\Packages\Application\Application;
use Common\Base\Illuminate\Support\ServiceProvider;

/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 16.08.2021
 * Time: 12:20
 */
abstract class Plugin extends ServiceProvider
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;
}

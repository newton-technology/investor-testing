<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 23.06.2020
 * Time: 22:23
 */

namespace Common\Packages\Application;

use Exception;

/**
 * Class LoadEnvironmentVariables
 * @package Common\App\Bootstrap
 */
class LoadEnvironmentVariables extends \Laravel\Lumen\Bootstrap\LoadEnvironmentVariables
{
    /**
     * @param array $errors
     * @throws Exception
     */
    protected function writeErrorAndDie(array $errors)
    {
        /** @noinspection PhpExpressionResultUnusedInspection */
        new Application($this->filePath);
        throw new Exception(implode(' ', $errors));
    }
}

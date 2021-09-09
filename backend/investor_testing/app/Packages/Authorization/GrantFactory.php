<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 18:08
 */

namespace Newton\InvestorTesting\Packages\Authorization;

use Throwable;

use Common\Base\Exception\Exception;

use Illuminate\Container\Container;

class GrantFactory
{
    /**
     * @throws Throwable
     */
    public function fromName(string $name): Grant
    {
        $raw = config("authorization.server.grants.{$name}");
        if (!is_array($raw) || empty($raw['service']) || !class_exists($raw['service'])) {
            throw Exception::unauthorized('invalid grant');
        }

        $grant = Container::getInstance()->make($raw['service']);
        if (!($grant instanceof Grant) || !($raw['properties']['enabled'] ?? false)) {
            throw Exception::unauthorized('invalid grant');
        }

        $grant->applyProperties($raw['properties']);

        return $grant;
    }
}

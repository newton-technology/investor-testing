<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.05.2020
 * Time: 21:07
 */

namespace Common\Base\Illuminate\Database;

use Common\Base\Illuminate\Database\Query\Builder;
use Common\Base\Illuminate\Database\Query\Grammars\PostgresGrammar;

use Illuminate\Database\Grammar;

class PostgresConnection extends \Illuminate\Database\PostgresConnection
{
    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new Builder($this, $this->getQueryGrammar(), $this->getPostProcessor());
    }

    /**
     * Get the default query grammar instance.
     *
     * @return Grammar|\Illuminate\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar());
    }
}

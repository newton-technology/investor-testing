<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 26.05.2020
 * Time: 21:08
 */

namespace Common\Base\Illuminate\Database\Query;

class Builder extends \Illuminate\Database\Query\Builder
{
    /**
     * Indicates if the query returns distinct results.
     *
     * @var bool|string[]
     */
    public $distinctOn = false;

    /**
     * Force the query to only return distinct on results.
     *
     * @param string[] $fields
     *
     * @return $this
     */
    public function distinctOn(array $fields)
    {
        $this->distinctOn = $fields;
        return $this;
    }
}

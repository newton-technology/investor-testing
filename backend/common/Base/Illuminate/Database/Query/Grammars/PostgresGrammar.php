<?php

namespace Common\Base\Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;

/**
 * Class PostgresGrammar
 * @package Common\Illuminate\Database\Query\Grammars
 */
class PostgresGrammar extends \Illuminate\Database\Query\Grammars\PostgresGrammar
{
    /**
     * Format date with milliseconds
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.v';
    }

    /**
     * Compile the "select *" portion of the query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (!is_null($query->aggregate)) {
            return null;
        }

        if ($query instanceof \Common\Base\Illuminate\Database\Query\Builder && $query->distinctOn !== false) {
            $select = 'select distinct on (' . $this->columnize($query->distinctOn) . ') ';
        } elseif (is_array($query->distinct)) {
            $select = 'select distinct on (' . $this->columnize($query->distinct) . ') ';
        } elseif ($query->distinct) {
            $select = 'select distinct ';
        } else {
            $select = 'select ';
        }

        return $select . $this->columnize($columns);
    }
}

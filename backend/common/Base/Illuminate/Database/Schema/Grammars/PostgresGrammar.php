<?php

namespace Common\Base\Illuminate\Database\Schema\Grammars;

use Illuminate\Support\Fluent;

class PostgresGrammar extends \Illuminate\Database\Schema\Grammars\PostgresGrammar
{
    /**
     * Create the column definition for an 'array' type.
     *
     * @param Fluent $column
     * @return string
     */
    protected function typeArray(Fluent $column)
    {
        return $column->get('arrayType');
    }
}

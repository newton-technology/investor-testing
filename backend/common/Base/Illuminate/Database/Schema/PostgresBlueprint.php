<?php

namespace Common\Base\Illuminate\Database\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;

class PostgresBlueprint extends Blueprint
{
    /**
     * Create a new 'array' column on the table.
     *
     * @param string $column
     * @param string $arrayType
     * @return Fluent
     */
    public function array($column, string $arrayType)
    {
        return $this->addColumn('array', $column, compact('arrayType'));
    }
}

<?php

namespace Common\Base\Illuminate\Database\Traits;

use Common\Base\Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Common\Base\Illuminate\Database\Schema\PostgresBlueprint;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;

trait PostgresMigrationTrait
{
    /**
     * @var Builder
     */
    protected $schema;

    public function __construct()
    {
        // register new grammar class
        DB::connection()->setSchemaGrammar(new PostgresGrammar());
        $this->schema = DB::connection()->getSchemaBuilder();

        // replace blueprint
        $this->schema->blueprintResolver(function($table, $callback) {
            return new PostgresBlueprint($table, $callback);
        });
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.08.2021
 * Time: 18:56
 */

namespace Newton\InvestorTesting\Packages\Export;

use Generator;
use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;
use Newton\InvestorTesting\Packages\Common\Test;

use Illuminate\Database\Query\Builder;

class TestExportRepository
{
    use IlluminateRepositoryTrait {
        getCollectionQuery as traitGetCollectionQuery;
    }

    protected string $connection = 'investor_testing';
    protected string $table = 'tests';
    protected string $entity = TestExportEntity::class;

    protected array $fields = [
        'user_id',
        'email',
        'category' => 'categories.code',
        "tests.status",
        'tests.updated_at',
    ];
    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @return Generator|TestExportEntity[]
     * @throws Throwable
     */
    public function getPassedTestsIterator(): Generator
    {
        return $this->getCollectionIterator(
            [
                ["{$this->table}.status", 'in', [Test::STATUS_PASSED, Test::STATUS_PROCESSING]],
            ],
            INF,
            0,
            [
                ["{$this->table}.user_id"],
                ['categories.id'],
            ]
        );
    }

    protected function getCollectionQuery(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Builder {
        return $this->traitGetCollectionQuery($filters, $limit, $offset, $orderBy, $fields)
            ->join('categories', "{$this->table}.category_id", 'categories.id')
            ->leftJoin('users', "{$this->table}.user_id", 'users.id');
    }
}

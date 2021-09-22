<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:37
 */

namespace Newton\InvestorTesting\Packages\Management;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

use Illuminate\Database\Query\Builder;

class TestListRepository
{
    use IlluminateRepositoryTrait {
        IlluminateRepositoryTrait::getCollectionQuery as traitGetCollectionQuery;
    }

    protected string $connection = 'investor_testing';
    protected string $table = 'tests';
    protected string $entity = TestListItem::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    protected array $fields = [
        'tests.id',
        'tests.created_at',
        'tests.updated_at',
        'tests.status',
        'user_id' => 'users.id',
        'user_email' => 'users.email'
    ];

    protected array $calculatedFields = [
        'category' => [
            'binding' => [['id', 'category_id']],
            'entity' => TestListCategory::class,
            'table' => 'categories',
            'fields' => 'id,code,name,description,description_short',
            'array' => false,
        ],
    ];

    /**
     * @return TestListItem[]
     */
    public function getTests(array $filters, float $limit = INF, int $offset = 0, array $orderBy = []): array
    {
        $this->transformTestsFilters($filters);

        foreach ($orderBy as &$order) {
            $order[0] = "{$this->table}.{$order[0]}";
        }

        return $this->getCollection($filters, $limit, $offset, $orderBy);
    }

    public function getTestsCount(array $filters): int
    {
        return $this->getCollectionQuery($filters)->count();
    }

    protected function transformTestsFilters(array &$filters)
    {
        foreach ($filters as &$filter) {
            if ($filter[0] === 'email') {
                $filter[0] = "users.email";
                continue;
            }
            $filter[0] = "{$this->table}.{$filter[0]}";
        }
    }

    protected function getCollectionQuery(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Builder {
        return $this->traitGetCollectionQuery($filters, $limit, $offset, $orderBy, $fields)
            ->join('users', 'users.id', "{$this->table}.user_id");
    }
}

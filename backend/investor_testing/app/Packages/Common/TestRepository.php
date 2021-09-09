<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:37
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class TestRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'tests';
    protected string $entity = Test::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @throws Throwable
     */
    public function addTest(Test $test)
    {
        $this->addEntityWithApplyResult($test);
    }

    public function updateTest(Test $test)
    {
        $this->updateEntityWithApplyResult($test);
    }

    public function getTest(int $userId, int $id): ?Test
    {
        return $this->getEntityByKey(
            [
                ['id', $id],
                ['user_id', $userId],
            ]
        );
    }

    /**
     * @return Test[]
     */
    public function getTests(array $filters, float $limit = INF, int $offset = 0, array $orderBy = []): array
    {
        return $this->getCollection($filters, $limit, $offset, $orderBy);
    }
}

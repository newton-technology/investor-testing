<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 13:10
 */

namespace Newton\InvestorTesting\Packages\Common;

use LogicException;
use stdClass;
use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;

class CategoryItemRepository
{
    use IlluminateRepositoryTrait {
        decodeRaw as traitDecodeRaw;
        getCollectionQuery as traitGetCollectionQuery;
    }

    protected string $connection = 'investor_testing';
    protected string $table = 'categories';
    protected string $entity = CategoryItem::class;
    protected array $dates = ['created_at'];
    protected array $fields = ['id', 'code', 'name', 'description', 'description_short', 'created_at', 'status'];

    /**
     * @return CategoryItem[]
     */
    public function getCategoryItems(int $userId): array
    {
        return $this->getCollection([['user_id', $userId]]);
    }

    public function getCategoryItem(int $userId, int $categoryId): ?CategoryItem
    {
        $items = $this->getCollection([['user_id', $userId], ['c.id', $categoryId]]);
        return empty($items) ? null : $items[0];
    }

    public function getCategoryItemByCode(int $userId, string $categoryCode): ?CategoryItem
    {
        $items = $this->getCollection([['user_id', $userId], ['c.code', $categoryCode]]);
        return empty($items) ? null : $items[0];
    }

    protected function getCollectionQuery(
        array $filters = [],
        float $limit = INF,
        int $offset = 0,
        array $orderBy = [],
        array $fields = ['*']
    ): Builder {
        if (empty($filters) || count($filters[0]) !== 2 || $filters[0][0] !== 'user_id') {
            throw new LogicException('invalid user_id filter');
        }
        $userId = $filters[0][1];
        unset($filters[0]);

        /** @var \Common\Base\Illuminate\Database\Query\Builder $builder */
        $builder = $this->getConnection()->table($this->table, 'c');
        $builder
            ->leftJoin(
                'tests as t',
                fn(JoinClause $join) => $join->on('c.id', '=', 't.category_id')
                    ->where('t.user_id', '=', $userId)
            )
            ->distinctOn(['c.id'])
            ->where('c.status', Category::STATUS_ENABLED)
            ->addSelect(['c.id', 'c.code', 'c.name', 'c.description', 'c.description_short', 'c.created_at', 't.status'])
            ->orderBy(new Expression("c.id, t.status != 'processing', t.status != 'passed', t.status != 'draft'"));

        $this->applyFilters($builder, $filters);

        return $builder;
    }

    /**
     * @throws Throwable
     */
    protected function decodeRaw(stdClass $raw, ?string $entity = null): CategoryItem
    {
        /** @var CategoryItem $object */
        $object = $this->traitDecodeRaw($raw, $entity);
        /** @noinspection PhpParamsInspection */
        return $object->setCategory(CategoryItemCategory::fromObject($raw));
    }
}

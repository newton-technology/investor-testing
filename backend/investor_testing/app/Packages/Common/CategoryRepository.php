<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 05.08.2021
 * Time: 18:29
 */

namespace Newton\InvestorTesting\Packages\Common;

use Throwable;

use Common\Base\Repositories\Database\IlluminateRepositoryTrait;

class CategoryRepository
{
    use IlluminateRepositoryTrait;

    protected string $connection = 'investor_testing';
    protected string $table = 'categories';
    protected string $entity = Category::class;

    protected array $dates = ['created_at', 'updated_at'];
    protected array $generatedFields = ['created_at', 'updated_at'];

    /**
     * @throws Throwable
     */
    public function addCategory(Category $category)
    {
        $this->addEntityWithApplyResult($category);
    }

    /**
     * @param Category[] $categories
     * @throws Throwable
     */
    public function addCategories(array $categories)
    {
        $this->addEntities($categories);
    }

    public function getCategory(int $categoryId): ?Category
    {
        return $this->getEntityById($categoryId);
    }

    /**
     * @return Category[]
     */
    public function getCategories(array $filters = []): array
    {
        return $this->getCollection($filters);
    }

    /**
     * @return Category[]
     */
    public function getCategoriesEnabled(): array
    {
        return $this->getCollection(
            [
                ['status', '!=', Category::STATUS_DISABLED],
            ]
        );
    }

    public function deleteAllCategories()
    {
        $this->deleteEntities([]);
    }

    public function getCategoryById(int $categoryId): ?Category
    {
        return $this->getEntityById($categoryId);
    }

    /**
     * Возвращает категорию по code
     *
     * @param string $code
     * @return Category|null
     */
    public function getCategoryByCode(string $code): ?Category
    {
        return $this->getEntityByKey([
            ['code', $code]
        ]);
    }

    /**
     * Редактирование категории
     *
     * @param Category $category
     */
    public function updateCategory(Category $category): void
    {
        $this->updateEntityWithApplyResult($category);
    }
}

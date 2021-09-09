<?php

namespace Plugins\Example\ExampleField\App\Packages\Common;

use Throwable;

use Newton\InvestorTesting\Packages\Common\CategoryItem;

class CategoryItemRepository extends \Newton\InvestorTesting\Packages\Common\CategoryItemRepository
{
    /**
     * @return CategoryItem[]
     * @throws Throwable
     */
    public function getCategoryItems(int $userId): array
    {
        return array_map(
            fn($item) => $this->transformCategoryItem($item),
            parent::getCategoryItems($userId)
        );
    }

    /**
     * @throws Throwable
     */
    public function getCategoryItem(int $userId, int $categoryId): ?CategoryItem
    {
        $item = parent::getCategoryItem($userId, $categoryId);
        if ($item !== null) {
            $this->transformCategoryItem($item);
        }

        return $item;
    }

    /**
     * @throws Throwable
     */
    private function transformCategoryItem(CategoryItem $categoryItem): CategoryItem
    {
        /** @var CategoryItemCategory $categoryItemCategory */
        $categoryItemCategory = CategoryItemCategory::fromArray(
            $categoryItem->getCategory()->toArray()
        );

        return $categoryItem->setCategory(
            $categoryItemCategory->setLogo('/resources/' . $categoryItem->getCategory()->getCode() . '.png')
        );
    }
}

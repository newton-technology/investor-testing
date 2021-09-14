<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 13:17
 */

namespace Newton\InvestorTesting\Packages\Common;

use Common\Base\Entities\ResponseableTrait;

class CategoryItem
{
    use ResponseableTrait;

    /**
     * Описание категории
     */
    protected CategoryItemCategory $category;

    /**
     * Статус теста
     */
    protected ?string $status;

    /**
     * @return CategoryItemCategory
     */
    public function getCategory(): CategoryItemCategory
    {
        return $this->category;
    }

    /**
     * @param CategoryItemCategory $category
     * @return CategoryItem
     */
    public function setCategory(CategoryItemCategory $category): self
    {
        $this->category = $category;
        return $this;
    }
}

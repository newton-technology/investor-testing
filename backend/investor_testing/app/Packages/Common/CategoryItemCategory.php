<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 06.08.2021
 * Time: 13:20
 */

namespace Newton\InvestorTesting\Packages\Common;

class CategoryItemCategory extends Category
{
    /**
     * @excludeFromResponse
     */
    protected int $createdAt;

    /**
     * @excludeFromResponse
     */
    protected ?int $updatedAt = null;

    /**
     * @excludeFromResponse
     */
    protected ?string $status = self::STATUS_ENABLED;
}

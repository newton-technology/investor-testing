<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 16.08.2021
 * Time: 16:16
 */

namespace Plugins\Example\ExampleField\App\Packages\Common;

class CategoryItemCategory extends \Newton\InvestorTesting\Packages\Common\CategoryItemCategory
{
    protected ?string $logo;

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): CategoryItemCategory
    {
        $this->logo = $logo;
        return $this;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 04.08.2021
 * Time: 18:21
 */

namespace Newton\InvestorTesting\Packages\Authorization;

interface Response
{
    public function toResponse(): array;
}

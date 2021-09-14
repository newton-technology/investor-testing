<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 19.08.2021
 * Time: 13:01
 */

namespace Common\Base\Http;

class JsonResponse extends \Illuminate\Http\JsonResponse
{
    protected ?float $sendTimestamp = null;

    public function send()
    {
        parent::send();
        $this->sendTimestamp = microtime(true);

        return $this;
    }

    public function getSendTimestamp(): ?float
    {
        return $this->sendTimestamp;
    }
}

<?php

namespace Common\Base\Utils;

use Generator;

abstract class BatchUtils
{
    /**
     * Вспомогательный метод для действий над пачками сущностей указанного размера в процессе итерации
     *
     * @param Generator $items Итератор сущностей
     * @param callable $cb Функция, получающая пачку сущностей в кач-ве аргумента
     * @param int $batchSize Размер пачки
     */
    public static function batch(Generator $items, callable $cb, int $batchSize = 100) {
        $batchCount = 0;
        $batchItems = [];
        while (true) {
            if ($batchCount >= $batchSize || !$items->valid()) {
                if (count($batchItems) > 0) {
                    $cb($batchItems);
                }
                $batchCount = 0;
                $batchItems = [];
            }
            if (!$items->valid()) {
                break;
            }
            $batchItems[] = $items->current();
            $batchCount++;
            $items->next();
        }
    }
}

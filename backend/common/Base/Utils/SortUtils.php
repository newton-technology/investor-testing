<?php

namespace Common\Base\Utils;

/**
 * Class SortUtils
 * @package Common\Utils
 */
abstract class SortUtils {

    public static function sortByTimestamp($array, $key='timestamp') {
        if (!$array || empty($array)) {
            return $array;
        }

        usort(
            $array,
            function ($first, $second) use ($key) {
                if ($first[$key] > $second[$key]) {
                    return 1;
                }
                if ($first[$key] < $second[$key]) {
                    return -1;
                }
                return 0;
            }
        );
        return $array;
    }

    public static function sortByTimestampDesc($array, $key='timestamp') {
        return ($array && !empty($array))
            ? array_values(array_reverse(SortUtils::sortByTimestamp($array, $key), true))
            : $array;
    }

    public static function sortByTimestampAndId($array, $key='timestamp') {
        if (!$array || empty($array)) {
            return $array;
        }

        usort(
            $array,
            function ($first, $second) use ($key) {
                if ($first[$key] !== $second[$key]) {
                    return $first[$key] - $second[$key];
                }

                return $first['id'] > $second['id']
                    ? 1
                    : ($first['id'] === $second['id'] ? 0 : -1);
            }
        );
        return $array;
    }

    public static function sortByTimestampAndIdDesc($array, $key='timestamp') {
        return ($array && !empty($array))
            ? array_values(array_reverse(SortUtils::sortByTimestampAndId($array, $key), true))
            : $array;
    }
}

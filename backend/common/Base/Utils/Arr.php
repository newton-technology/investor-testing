<?php

namespace Common\Base\Utils;

abstract class Arr
{
    /**
     * Установить элемент массива заданным значением, используя "точечную" нотацию.
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    public static function set(array &$array, string $key, $value): array
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Вернет элемент из массива, используя "точечную" нотацию.
     *
     * @param array $array
     * @param string|int $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Определить, доступно ли данное значение массиву.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value);
    }

    /**
     * Определить, существует ли данный ключ в предоставленном массиве.
     *
     * @param array $array
     * @param string|int $key
     *
     * @return bool
     */
    public static function exists(array $array, $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Возвращает различающиеся элементы массива
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function arrayDiffAssocRecursive(array $array1, array $array2): array
    {
        $difference = [];

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $newDiff = self::arrayDiffAssocRecursive($value, $array2[$key]);
                    if (!empty($newDiff)) {
                        $difference[$key] = $newDiff;
                    }
                }
            } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        foreach (array_diff(array_keys($array2), array_keys($array1)) as $key) {
            $difference[$key] = null;
        }

        return $difference;
    }
}

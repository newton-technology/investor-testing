<?php

namespace Common\Base\Utils;

/**
 * Class NumberUtils
 * @package Common\Utils
 */
abstract class NumberUtils
{
    /**
     * Функция проверки равенства чисел с плавающей точкой с заданной точностью
     *
     * Реализация взята из данного источника https://floating-point-gui.de/errors/comparison/
     *
     * @param float $a
     * @param float $b
     * @param float $epsilon
     * @return bool
     */
    public static function nearlyEqual(float $a, float $b, float $epsilon = PHP_FLOAT_EPSILON): bool
    {
        $absA = abs($a);
        $absB = abs($b);
        $diff = abs($a - $b);

        if ($a == $b) {
            return true;
        } else if ($a == 0 || $b == 0 || ($absA + $absB < PHP_FLOAT_MIN)) {
            return $diff < ($epsilon * PHP_FLOAT_MIN);
        } else {
            return $diff / min(($absA + $absB), PHP_FLOAT_MAX) < $epsilon;
        }
    }

    /**
     * Функция проверки равенства чисел с плавающей точкой с заданной точностью
     * при условии, что одно из чисел может быть нуллом.
     * Два нулла считаются эквивалентными
     *
     * @param float|null $a
     * @param float|null $b
     * @param float $epsilon
     * @return bool
     */
    public static function nearlyEqualNullable(?float $a, ?float $b, float $epsilon = PHP_FLOAT_EPSILON): bool
    {
        if (is_null($a)) {
            return is_null($b);
        }
        if (is_null($b)) {
            return false;
        }
        return self::nearlyEqual($a, $b, $epsilon);
    }
}

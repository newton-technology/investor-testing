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
}

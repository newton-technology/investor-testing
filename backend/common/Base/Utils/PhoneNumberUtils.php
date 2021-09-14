<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 10.07.2020
 * Time: 00:27
 */

namespace Common\Base\Utils;

use Exception;

/**
 * Class PhoneNumberUtils
 * @package Common\Utils
 */
abstract class PhoneNumberUtils
{
    /**
     * @param string $number
     * @throws Exception
     */
    public static function sanitizePhoneNumber(string &$number)
    {
        $intNumber = str_replace(['+', '-'], '', filter_var($number, FILTER_SANITIZE_NUMBER_INT));

        if (strlen($intNumber) > 15) {
            throw new Exception('phone number is too long');
        }

        $number = (substr($number, 0, 1) === '+' ? '+' : '') . $intNumber;
    }

    /**
     * @param string $number
     * @throws Exception
     */
    public static function transformRussianPhoneNumber(string &$number)
    {
        $intNumber = str_replace(['+', '-'], '', filter_var($number, FILTER_SANITIZE_NUMBER_INT));

        if (strlen($intNumber) !== 11) {
            throw new Exception('wrong russian phone number');
        }
        if (substr($intNumber, 0, 1) !== '7') {
            throw new Exception('russian phone number must start with +7');
        }

        $number = "+$intNumber";
    }

    /**
     * @param string $number
     * @return string
     * @throws Exception
     */
    public static function getRussianPhoneNumber(string $number)
    {
        self::sanitizePhoneNumber($number);
        self::transformRussianPhoneNumber($number);

        return $number;
    }
}

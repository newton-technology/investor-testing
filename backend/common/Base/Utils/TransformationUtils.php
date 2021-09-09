<?php

namespace Common\Base\Utils;

use DateTime;
use stdClass;

abstract class TransformationUtils
{
    /**
     * Массив строк трансформации из camelCase в under_score
     *
     * @var array<string, string>
     */
    private static array $camelCaseToUnderScore = [];

    /**
     * Массив строк трансформации из under_score в camelCase
     *
     * @var array<string, string>
     */
    private static array $underScoreToCamelCase = [];

    /**
     * @param string $camelCaseString
     *
     * @return string
     */
    public static function stringCamelCaseToUnderScore(string $camelCaseString): string
    {
        $underScoreString = static::$camelCaseToUnderScore[$camelCaseString] ?? null;
        if ($underScoreString === null) {
            $underScoreString = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $camelCaseString));
            static::$camelCaseToUnderScore[$camelCaseString] = $underScoreString;
        }

        return $underScoreString;
    }

    /**
     * @param string $underScoreString
     *
     * @return string
     */
    public static function stringUnderScoreToCamelCase(string $underScoreString): string
    {
        $camelCaseString = static::$underScoreToCamelCase[$underScoreString] ?? null;
        if ($camelCaseString === null) {
            $camelCaseString = str_replace('_', '', lcfirst(ucwords($underScoreString, '_')));
            static::$underScoreToCamelCase[$underScoreString] = $camelCaseString;
        }

        return $camelCaseString;
    }

    /**
     * Transform property names from under_score to camelCase
     *
     * @param stdClass $underScoreObject
     *
     * @return stdClass
     */
    public static function objectUnderScoreToCamelCase(stdClass $underScoreObject): stdClass
    {
        $camelCaseObject = new stdClass();
        $properties = (array)$underScoreObject;

        foreach ($properties as $name => $value) {
            $name = self::stringUnderScoreToCamelCase($name);
            $camelCaseObject->$name = $value;
        }

        return $camelCaseObject;
    }

    /**
     * @param array $array
     *
     * @return stdClass
     */
    public static function arrayToObject(array $array): stdClass
    {
        $object = json_decode(json_encode($array));
        return empty($object) ? new stdClass() : $object;
    }

    /**
     * @param string|null $time
     * @param string|null $timezone
     * @return int|null
     */
    public static function stringToTimestamp(?string $time, string $timezone = null): ?int
    {
        if (!empty($timezone)) {
            $time .= " $timezone";
        }
        $timestamp = strtotime($time);
        if ($timestamp === false) {
            return null;
        }
        return $timestamp;
    }

    /**
     * @deprecated use DateTimeUtils::fromTimestamp()
     *
     * @param int|float $timestamp
     * @param bool $withMilliseconds
     *
     * @return bool|DateTime
     */
    public static function timestampToDateTime($timestamp, $withMilliseconds = false): DateTime
    {
        if (strpos($timestamp, '.') === false || !$withMilliseconds) {
            return DateTime::createFromFormat('U', (int)$timestamp);
        }

        [$seconds, $milliseconds] = explode('.', $timestamp);
        if (empty($milliseconds)) {
            return DateTime::createFromFormat('U', $seconds);
        }

        return DateTime::createFromFormat('U.u', $timestamp);
    }

    /**
     * @param string|null $textcyr
     * @param string|null $textlat
     *
     * @return string|null
     */
    public static function transliterate(string $textcyr = null, string $textlat = null): ?string
    {
        $cyr = [
            'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
            'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я'
        ];
        $lat = [
            'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q',
            'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q'
        ];

        if ($textcyr) {
            return str_replace($cyr, $lat, $textcyr);
        }

        if ($textlat) {
            return str_replace($lat, $cyr, $textlat);
        }

        return null;
    }

    /**
     * Преобразует значение в json-строку
     *
     * @param $value
     * @param int $flags
     * @param int $depth
     * @return false|string
     */
    public static function toJson($value, $flags = JSON_UNESCAPED_UNICODE, $depth = 512)
    {
        return json_encode($value, $flags, $depth);
    }
}

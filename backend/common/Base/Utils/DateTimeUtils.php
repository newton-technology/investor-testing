<?php

declare(strict_types=1);

namespace Common\Base\Utils;

use DateInterval;
use DateTime;
use Exception;

abstract class DateTimeUtils
{
    /**
     * Получить экземпляр класса текущей даты
     *
     * @return DateTime
     */
    public static function now(): DateTime
    {
        return new DateTime();
    }

    /**
     * Получить экземпляр времени в заданном формате
     *
     * @param string $format
     *
     * @return string
     */
    public static function formattedNow(string $format): string
    {
        return static::now()->format($format);
    }

    /**
     * Получить экземпляр вчерашней даты
     *
     * @return DateTime
     */
    public static function yesterday(): DateTime
    {
        return (new DateTime())->sub(new DateInterval('P1D'))->setTime(0, 0);
    }

    /**
     * Создать экземпляр из timestamp
     *
     * @param int|float $timestamp
     *
     * @return DateTime
     */
    public static function fromTimestamp($timestamp): DateTime
    {
        $timestampAsString = (string)$timestamp;

        if (strpos($timestampAsString, '.') === false) {
            return DateTime::createFromFormat('U', $timestampAsString);
        }

        $timeData = explode('.', $timestampAsString);
        $seconds = $timeData[0] ?? null;
        $milliseconds = $timeData[1] ?? null;
        if (empty($milliseconds)) {
            return DateTime::createFromFormat('U', $seconds);
        }

        return DateTime::createFromFormat('U.u', $timestampAsString);
    }

    /**
     * Создать объект DateTime по переданной строке
     *
     * Также можно задать формат времени, если временная строка имеет нестандартный вид
     *
     * @param string $dateTime
     * @param string|null $format
     *
     * @return DateTime
     * @throws Exception
     */
    public static function fromString(string $dateTime, string $format = null): DateTime
    {
        if (!empty($format)) {
            return DateTime::createFromFormat($format, $dateTime);
        }

        return new DateTime($dateTime);
    }

    /**
     * Проверить даты на идентичность до дня
     *
     * @param DateTime $date1
     * @param DateTime $date2
     * @return bool
     */
    public static function equalByDate(DateTime $date1, DateTime $date2): bool
    {
        return $date1->format('Ymd') === $date2->format('Ymd');
    }

    /**
     * Проверить временные метки на идентичность до дня
     *
     * @param int $date1
     * @param int $date2
     * @return bool
     */
    public static function timestampsEqualByDate(int $date1, int $date2): bool
    {
        return date('Ymd', $date1) === date('Ymd', $date2);
    }

    /**
     * Проверить, что одна дата после другой
     *
     * @param DateTime $date1
     * @param DateTime $date2
     * @return bool
     */
    public static function afterDate(DateTime $date1, DateTime $date2): bool
    {
        return $date1->format('Ymd') > $date2->format('Ymd');
    }

    /**
     * Проверить, что одна дата до другой
     *
     * @param DateTime $date1
     * @param DateTime $date2
     * @return bool
     */
    public static function beforeDate(DateTime $date1, DateTime $date2): bool
    {
        return $date1->format('Ymd') < $date2->format('Ymd');
    }
}

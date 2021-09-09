<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 18.03.2021
 * Time: 13:33
 */

namespace Common\Base\Utils;

/**
 * Класс для формирования текстовой ascii-таблицы
 */
abstract class TextTable
{
    /**
     * @param string[][] $data
     * @param string[] $columnNames заголовки столбцов
     * @param string[] $summaryNames заголовки столбцов по которым необходимо посчитать сумму
     * @return string
     */
    public static function fromArray(array $data, array $columnNames = [], $summaryNames = []): string
    {
        $cellLengths = [];
        $summaryValues = [];
        foreach (array_merge($data, [$columnNames]) as $row) {
            $row = array_values((array)$row);
            foreach ($row as $i => $cell) {
                if ($i < count($columnNames)) {
                    if (in_array($cell, $summaryNames, true)) {
                        $summaryValues[$i] = 0;
                    }
                }
                $cellLengths[$i] = max(mb_strlen($cell, 'utf-8'), $cellLengths[$i] ?? 0);
            }
        }

        if (!empty($summaryValues)) {
            foreach ($data as $row) {
                foreach ($summaryValues as $i => $value) {
                    $cell = $row[$i];
                    $summaryValues[$i] = $value + (float) $cell;
                    $cellLengths[$i] = max(mb_strlen($summaryValues[$i], 'utf-8'), $cellLengths[$i] ?? 0);
                }
            }
        }

        $output = '';
        // сумма_длин + количество_обрамляющих_пробелов_и_пайп + ведущий_пайп
        $line = str_pad('', array_sum($cellLengths) + count($cellLengths) * 3 + 1, '-');

        $output .= $line . PHP_EOL;
        $output .= '|';
        for ($i = 0; $i < count($cellLengths); $i++) {
            $output .= ' ' . str_pad($columnNames[$i] ?? '', $cellLengths[$i], ' ', STR_PAD_LEFT) . ' |';
        }
        $output .= PHP_EOL;
        $output .= $line . PHP_EOL;

        foreach ($data as $row) {
            $row = array_values((array)$row);
            $output .= '|';
            foreach ($row as $i => $cell) {
                $output .= ' ' . str_pad('', $cellLengths[$i] - mb_strlen($cell, 'utf-8')) . $cell . ' |';
            }
            $output .= PHP_EOL;
        }

        if (!empty($summaryValues)) {
            $output .= $line . PHP_EOL;
            $output .= '|';
            for ($i = 0; $i < count($cellLengths); $i++) {
                $cell = $summaryValues[$i] ?? '';
                $output .= ' ' . str_pad('', $cellLengths[$i] - mb_strlen($cell, 'utf-8')) . $cell . ' |';
            }
            $output .= PHP_EOL;
        }

        $output .= $line . PHP_EOL;

        return $output;
    }
}

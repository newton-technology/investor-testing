<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.05.2020
 * Time: 15:12
 */

namespace Common\Base\Illuminate\Filesystem;

use Exception;

/**
 * Class FileSystem
 * @package Common\Support
 */
class FileSystem extends \Illuminate\Filesystem\Filesystem
{
    /**
     * @param $filename
     * @return string
     * @throws Exception
     */
    public function getAbsoluteFilename($filename): string
    {
        $path = [];
        foreach (explode('/', $filename) as $part) {
            // ignore parts that have no value
            if (empty($part) || $part === '.') continue;

            if ($part !== '..') {
                // cool, we found a new part
                array_push($path, $part);
            } else if (count($path) > 0) {
                // going back up? sure
                array_pop($path);
            } else {
                // now, here we don't like
                throw new Exception('Climbing above the root is not permitted.');
            }
        }

        return '/' . join('/', $path);
    }

}

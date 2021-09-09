<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 27.05.2020
 * Time: 14:46
 */

namespace Common\Base\Utils;

use Exception;

/**
 * Class Git
 * @package Common\Base\Utils\Git
 */
class Git
{
    /**
     * Git constructor.
     * @throws Exception
     */
    public function __construct()
    {
        exec('git --version', $output);
        if (count($output) !== 1 || preg_match('/^git version 2.*$/', $output[0]) !== 1) {
            throw new Exception('git 2.* is required to create this object');
        }
    }

    /**
     * @throws Exception
     */
    public function getRootPath()
    {
        exec('git rev-parse --show-toplevel', $output);
        if (count($output) !== 1) {
            throw new Exception('git root path not found');
        }
        return $output[0];
    }

    /**
     * @param string $source
     * @return mixed
     * @throws Exception
     */
    public function getFiles(string $source)
    {
        $root = $this->getRootPath();
        $count = 1;
        $relative = substr(str_replace($root, '', $source, $count), 1);
        exec("git ls-tree --full-tree -r --name-only HEAD:{$relative}", $output);

        return $output;
    }

}

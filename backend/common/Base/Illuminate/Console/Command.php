<?php
/**
 * Created by PhpStorm.
 * User: Dmitry Loshmanov
 * Date: 12.04.2021
 * Time: 16:12
 */

namespace Common\Base\Illuminate\Console;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Command extends \Illuminate\Console\Command
{
    /**
     * @inheritdoc
     */
    public function line($string, $style = null, $verbosity = null, $newLine = true)
    {
        if ($newLine) {
            parent::line($string, $style, $verbosity);
        } else {
            $styled = $style ? "<$style>$string</$style>" : $string;
            $this->output->write($styled, false, $this->parseVerbosity($verbosity));
        }
    }

    /**
     * @inheritdoc
     */
    public function info($string, $verbosity = null, $newLine = true)
    {
        $this->line($string, 'info', $verbosity, $newLine);
    }

    /**
     * @inheritdoc
     */
    public function comment($string, $verbosity = null, $newLine = true)
    {
        $this->line($string, 'comment', $verbosity, $newLine);
    }

    /**
     * @inheritdoc
     */
    public function question($string, $verbosity = null, $newLine = true)
    {
        $this->line($string, 'question', $verbosity, $newLine);
    }

    /**
     * @inheritdoc
     */
    public function error($string, $verbosity = null, $newLine = true)
    {
        $this->line($string, 'error', $verbosity, $newLine);
    }

    /**
     * @inheritdoc
     */
    public function warn($string, $verbosity = null, $newLine = true)
    {
        if (! $this->output->getFormatter()->hasStyle('warning')) {
            $style = new OutputFormatterStyle('yellow');
            $this->output->getFormatter()->setStyle('warning', $style);
        }

        $this->line($string, 'warning', $verbosity, $newLine);
    }
}

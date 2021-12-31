<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Console\Output;

use Symfony\Component\Console\Output\ConsoleOutput as BaseConsoleOutput;

class ConsoleOutput extends BaseConsoleOutput
{
    const FORMAT_INFO = 'info'; /* Green text */
    const FORMAT_COMMENT = 'comment'; /* Yellow text */
    const FORMAT_QUESTION = 'question'; /* Black text on a cyan background */
    const FORMAT_ERROR = 'error'; /* White text on a red background */

    /**
     * @param $messages
     * @param bool $newline
     * @param int $options
     * @return void
     */
    public function writeInfo($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_INFO);
        $this->write($messages, $newline, $options);
    }

    /**
     * @param $messages
     * @param int $options
     * @return void
     */
    public function writelnInfo($messages, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_INFO);
        $this->writeln($messages, $options);
    }

    /**
     * @param $messages
     * @param bool $newline
     * @param int $options
     * @return void
     */
    public function writeComment($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_COMMENT);
        $this->write($messages, $newline, $options);
    }

    /**
     * @param $messages
     * @param int $options
     * @return void
     */
    public function writelnComment($messages, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_COMMENT);
        $this->writeln($messages, $options);
    }

    /**
     * @param $messages
     * @param bool $newline
     * @param int $options
     * @return void
     */
    public function writeQuestion($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_QUESTION);
        $this->write($messages, $newline, $options);
    }

    /**
     * @param $messages
     * @param int $options
     * @return void
     */
    public function writelnQuestion($messages, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_QUESTION);
        $this->writeln($messages, $options);
    }

    /**
     * @param $messages
     * @param bool $newline
     * @param int $options
     * @return void
     */
    public function writeError($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_ERROR);
        $this->write($messages, $newline, $options);
    }

    /**
     * @param $messages
     * @param int $options
     * @return void
     */
    public function writelnError($messages, int $options = self::OUTPUT_NORMAL)
    {
        $this->wrapMessages($messages, self::FORMAT_ERROR);
        $this->writeln($messages, $options);
    }

    /**
     * @param array|string $messages
     * @param string $pattern
     * @return void
     */
    private function wrapMessages(&$messages, string $pattern)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as &$message) {
            $message = "<$pattern>$message</$pattern>";
        }
    }
}
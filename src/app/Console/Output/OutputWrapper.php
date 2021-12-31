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

use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

class OutputWrapper
{
    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var ConsoleSectionOutput[]
     */
    private array $sections = [];

    public function __construct(
        OutputInterface $output
    ) {
        $this->output = $output;
    }

    /**
     * @param string $messages
     * @return void
     */
    public function overwrite(string $messages)
    {
        $messages = explode(PHP_EOL, $messages);
        foreach ($messages as $message) {
            if (empty($message)) {
                continue;
            }
            list($process, $containerId) = explode(' ', $message);
            $this->sections[$containerId] = $this->sections[$containerId] ?? $this->output->section();
            $this->sections[$containerId]->overwrite($message);
        }
    }
}

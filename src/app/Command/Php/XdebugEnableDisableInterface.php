<?php

namespace MagedIn\Lab\Command\Php;

use Symfony\Component\Console\Output\OutputInterface;

interface XdebugEnableDisableInterface
{
    /**
     * @param OutputInterface $output
     * @return void
     */
    public function writeCheckResult(OutputInterface $output): void;

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function writeEndResult(OutputInterface $output): void;

    /**
     * @return int
     */
    public function getCheckCode(): int;

    /**
     * @return string
     */
    public function getSedPattern(): string;
}

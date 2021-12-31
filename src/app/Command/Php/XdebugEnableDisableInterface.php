<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2021 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\Command\Php;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface XdebugEnableDisableInterface
{
    /**
     * @param OutputInterface $output
     * @return void
     */
    public function writeCheckResult(OutputInterface $output): void;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function writeEndResult(InputInterface $input, OutputInterface $output): void;

    /**
     * @return int
     */
    public function getCheckCode(): int;

    /**
     * @param InputInterface $input
     * @return string
     */
    public function getSedPattern(InputInterface $input): string;

    /**
     * @return string
     */
    public function getIniFilename(): string;
}

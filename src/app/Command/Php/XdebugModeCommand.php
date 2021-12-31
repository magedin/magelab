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

namespace MagedIn\Lab\Command\Php;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugModeCommand extends XdebugAbstractCommand implements XdebugEnableDisableInterface
{
    protected function configure()
    {
        $this->addArgument(
            'mode',
            InputArgument::OPTIONAL,
            'Accepted modes: off, develop, coverage, debug, gcstats, profile, trace',
            'debug'
        );
    }

    /**
     * @inheritDoc
     */
    public function writeCheckResult(OutputInterface $output): void
    {
        $output->writeln('');
    }

    /**
     * @inheritDoc
     */
    public function writeEndResult(InputInterface $input, OutputInterface $output): void
    {
        $mode = $input->getArgument('mode');
        $output->writeln("<fg=green>Xdebug mode was changed to '$mode'.</>");
    }

    /**
     * @inheritDoc
     */
    public function getSedPattern(InputInterface $input): string
    {
        $mode = $input->getArgument('mode');
        $this->validateRequestMode($mode);
        return $this->xdebugInfo->getSedModeChangePattern($mode);
    }

    /**
     * @inheritDoc
     */
    public function getCheckCode(): int
    {
        /** Xdebug is already ENABLED. */
        return Command::SUCCESS;
    }

    /**
     * @return string
     */
    public function getIniFilename(): string
    {
        return $this->xdebugInfo->getConfigFileName();
    }

    /**
     * @inheritDoc
     */
    protected function skipChecks(InputInterface $input): bool
    {
        return true;
    }

    /**
     * @param string $mode
     * @return void
     */
    private function validateRequestMode(string $mode): void
    {
        $acceptedModes = ['off', 'develop', 'coverage', 'debug', 'gcstats', 'profile', 'trace'];
        if (!in_array($mode, $acceptedModes)) {
            $message = sprintf(
                "The mode '$mode' is not accepted. Please use one of the following: %s",
                implode(', ', $acceptedModes)
            );
            throw new InvalidArgumentException($message);
        }
    }
}

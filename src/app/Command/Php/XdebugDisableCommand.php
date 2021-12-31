<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Php;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XdebugDisableCommand extends XdebugAbstractCommand implements XdebugEnableDisableInterface
{
    /**
     * @inheritDoc
     */
    public function writeCheckResult(OutputInterface $output): void
    {
        $output->writeln('<fg=yellow>Xdebug is already DISABLED. No changes applied.</>');
    }

    /**
     * @inheritDoc
     */
    public function writeEndResult(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<fg=yellow>Xdebug is now DISABLED.</>');
    }

    /**
     * @inheritDoc
     */
    public function getSedPattern(InputInterface $input): string
    {
        return $this->xdebugInfo->getSedDisablePattern();
    }

    /**
     * @inheritDoc
     */
    public function getCheckCode(): int
    {
        /** Xdebug is already DISABLED. */
        return Command::FAILURE;
    }

    /**
     * @return string
     */
    public function getIniFilename(): string
    {
        return $this->xdebugInfo->getActivateFileName();
    }
}

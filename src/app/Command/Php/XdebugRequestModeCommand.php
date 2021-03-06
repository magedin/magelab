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

class XdebugRequestModeCommand extends XdebugAbstractCommand implements XdebugEnableDisableInterface
{
    protected function configure()
    {
        $this->addArgument(
            'mode',
            InputArgument::REQUIRED,
            'Accepted request modes: yes, no, trigger, default',
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
        $output->writeln("<fg=green>Xdebug request mode was changed to '$mode'.</>");
    }

    /**
     * @inheritDoc
     */
    public function getSedPattern(InputInterface $input): string
    {
        $mode = $input->getArgument('mode');
        $this->validateRequestMode($mode);
        return $this->xdebugInfo->getSedRequestModeChangePattern($mode);
    }

    /**
     * @inheritDoc
     */
    public function getCheckCode(): bool
    {
        /** Xdebug is already ENABLED. */
        return true;
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
     * If Xdebug is disabled then no need to reload the FPM service.
     * @return bool
     */
    protected function canReloadServices(): bool
    {
        return $this->xdebugStatusCommandExecutor->execute();
    }

    /**
     * @param string $mode
     * @return void
     */
    private function validateRequestMode(string $mode): void
    {
        $acceptedModes = ['no', 'yes', 'trigger', 'default'];
        if (!in_array($mode, $acceptedModes)) {
            $message = sprintf(
                "The request mode '$mode' is not accepted. Please use one of the following: %s",
                implode(', ', $acceptedModes)
            );
            throw new InvalidArgumentException($message);
        }
    }
}

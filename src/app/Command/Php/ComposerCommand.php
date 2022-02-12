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

use MagedIn\Lab\Command\ProxyCommand;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommand extends ProxyCommand
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExecCommandBuilder;

    /**
     * @var array
     */
    protected array $protectedOptions = ['--one', '-1'];

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder,
        NonDefaultOptions $nonDefaultOptions,
        string $name = null
    ) {
        parent::__construct($nonDefaultOptions, $name);
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = $this->isComposerOne() ? 'composer1' : 'composer';
        $cleanOptions = array_diff($this->getShiftedArgv(), $this->getProtectedOptions());
        $command = array_merge($command, $cleanOptions);

        Process::run($command, [
            'tty' => true,
        ]);
        return Command::SUCCESS;
    }

    /**
     * @return bool
     */
    private function isComposerOne(): bool
    {
        $args = $this->getShiftedArgv();
        return in_array('--one', $args) || in_array('-1', $args);
    }
}

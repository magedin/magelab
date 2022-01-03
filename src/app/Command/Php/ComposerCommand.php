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

use MagedIn\Lab\Command\SpecialCommand;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommand extends SpecialCommand
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

    protected function configure()
    {
        $this->addArgument(
            'composer-command',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Composer command'
        );

        $this->addOption(
            'one',
            '1',
            InputOption::VALUE_NONE,
            'Use composer 1'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = $input->getOption('one') ? 'composer1' : 'composer';
        $cleanOptions = array_diff($this->getShiftedArgv(), $this->getProtectedOptions());
        $command = array_merge($command, $cleanOptions);

        Process::run($command, [
            'tty' => true,
            'timeout' => null, /** In case of composer let's just remove the time limit. */
        ]);
        return Command::SUCCESS;
    }
}

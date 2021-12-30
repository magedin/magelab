<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command\Php;

use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommand extends Command
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
    }

    protected function configure()
    {
        $this->addArgument(
            'composer-command',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Composer command'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposeExecCommandBuilder->build();
        $command[] = 'php'; /** container where the exec will run. */
        $command[] = 'composer';
        $command = array_merge($command, $input->getArgument('composer-command'));

        Process::run($command, [
            'tty' => true,
            'timeout' => null, /** In case of composer let's just remove the time limit. */
        ]);
        return Command::SUCCESS;
    }
}

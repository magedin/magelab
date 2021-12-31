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

namespace MagedIn\Lab\Command\Container;

use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SshCommand extends Command
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
            'service',
            InputArgument::OPTIONAL,
            'Select a specific service container'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getArgument('service')) {
            $this->showOptions($output);
            return Command::FAILURE;
        }

        $command = $this->dockerComposeExecCommandBuilder->build();
        $command[] = $input->getArgument('service');
        $command[] = 'bash';

        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    private function showOptions(OutputInterface $output): void
    {
        $output->writeln([
            '<comment>You need to provid one of the following service names to SSH command:</comment>',
            null
        ]);
        $command = $this->getApplication()->find('status');
        $input = ObjectManager::getInstance()->create(ArrayInput::class, [
            'parameters' => [
                '--services' => true
            ]
        ]);
        $command->run($input, $output);
    }
}

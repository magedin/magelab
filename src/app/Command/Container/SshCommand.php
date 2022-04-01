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
use MagedIn\Lab\Console\Input\ArrayInputFactory;
use MagedIn\Lab\Helper\Console\Question;
use MagedIn\Lab\Helper\DockerComposeServicesList;
use MagedIn\Lab\Helper\DockerServiceState;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SshCommand extends Command
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    /**
     * @var ArrayInputFactory
     */
    private ArrayInputFactory $arrayInputFactory;

    /**
     * @var Question
     */
    private Question $question;

    /**
     * @var DockerComposeServicesList
     */
    private DockerComposeServicesList $servicesList;

    /**
     * @var DockerServiceState
     */
    private DockerServiceState $serviceState;

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        ArrayInputFactory $arrayInputFactory,
        Question $question,
        DockerComposeServicesList $servicesList,
        DockerServiceState $serviceState,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
        $this->arrayInputFactory = $arrayInputFactory;
        $this->question = $question;
        $this->servicesList = $servicesList;
        $this->serviceState = $serviceState;
    }

    protected function configure()
    {
        $this->addArgument(
            'service',
            InputArgument::OPTIONAL,
            'Select a specific service container'
        );

        $this->addOption(
            'root',
            'r',
            InputOption::VALUE_NONE,
            'SSH as root user.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->serviceState->isRunning()) {
            $output->writeln('The services are not running.');
            return Command::FAILURE;
        }

        $service = $input->getArgument('service');
        if (!$service) {
            $services = $this->servicesList->getNames();
            $service = $this->question->choose($input, $output, 'To which service?', $services);
            if (!$service) {
                return Command::FAILURE;
            }
        }

        $subcommands = [$service, 'bash'];
        $options = [];
        if ($input->getOption('root')) {
            $options = ['root' => true];
        }
        $command = $this->dockerComposeExecCommandBuilder->build($subcommands, $options);
        Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);

        return Command::SUCCESS;
    }
}

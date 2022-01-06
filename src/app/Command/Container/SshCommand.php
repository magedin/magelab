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
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        ArrayInputFactory $arrayInputFactory,
        Question $question,
        DockerComposeServicesList $servicesList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
        $this->arrayInputFactory = $arrayInputFactory;
        $this->question = $question;
        $this->servicesList = $servicesList;
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
        $service = $input->getArgument('service');
        if (!$service) {
            $services = $this->servicesList->getNames();
            $service = $this->question->choose($input, $output, 'To which service?', $services);
            if (!$service) {
                return Command::FAILURE;
            }
        }

        $subcommands = [$service, 'bash'];
        $command = $this->dockerComposeExecCommandBuilder->build($subcommands);
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
            '<comment>You need to provide one of the following service names to SSH command:</comment>',
            null
        ]);
        $command = $this->getApplication()->find('status');
        $input = $this->arrayInputFactory->create(['--services' => true]);
        $command->run($input, $output);
    }
}

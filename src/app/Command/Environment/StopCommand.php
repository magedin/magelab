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

namespace MagedIn\Lab\Command\Environment;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Console\Output\OutputWrapperBuilder;
use MagedIn\Lab\Helper\DockerServiceState;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends Command
{
    /**
     * @var DockerServiceState
     */
    private DockerServiceState $dockerServiceState;

    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    /**
     * @var OutputWrapperBuilder
     */
    private OutputWrapperBuilder $outputWrapperBuilder;

    public function __construct(
        DockerServiceState $dockerServiceState,
        DockerCompose $dockerComposeCommandBuilder,
        OutputWrapperBuilder $outputWrapperBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerServiceState = $dockerServiceState;
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
        $this->outputWrapperBuilder = $outputWrapperBuilder;
    }

    protected function configure()
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force to stop the containers.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force') && $this->dockerServiceState->isStopped()) {
            $output->writeln('The services are already down.');
            return Command::SUCCESS;
        }

        $command = $this->dockerComposeCommandBuilder->build(['stop']);
        $output->writelnInfo('Stopping the containers.');
        $outputWrapper = $this->outputWrapperBuilder->build($output);
        Process::run($command, [
            'callback' => function ($type, $buffer) use ($outputWrapper) {
                $outputWrapper->overwrite($buffer);
            },
        ]);

        return Command::SUCCESS;
    }
}

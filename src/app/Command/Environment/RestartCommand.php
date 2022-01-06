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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestartCommand extends Command
{
    /**
     * @var DockerServiceState
     */
    private DockerServiceState $dockerServiceState;

    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

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
        $this->addArgument(
            'services',
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The services to be restarted.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->dockerComposeCommandBuilder->build(['restart']);
        if ($services = $input->getArgument('services')) {
            $command = array_merge($command, $services);
        }

        $output->writelnInfo('Restarting the service containers.');
        $outputWrapper = $this->outputWrapperBuilder->build($output);
        Process::run($command, [
            'callback' => function ($type, $buffer) use ($outputWrapper) {
                $outputWrapper->overwrite($buffer);
            },
        ]);
        $output->writelnInfo('Containers has been restarted.');
        return Command::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace MageLab\Command;

use MageLab\CommandBuilder\DockerCompose;
use MageLab\Config\DockerLab\BasePath;
use MageLab\Config\Helper\OperatingSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DockerComposeCommand extends Command
{
    protected function configure()
    {
        $this->addArgument(
            'subcommand',
            InputArgument::REQUIRED,
            'The command that must run on Docker Compose.',
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandBuilder = new DockerCompose();
        $command = $commandBuilder->build();

        $subcommand = $input->getArgument('subcommand');
        $command[] = $subcommand;

        $process = new Process($command);
        $process->run();

        $output->writeln($process->getOutput());

        return Command::SUCCESS;
    }
}

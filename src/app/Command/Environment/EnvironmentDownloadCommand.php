<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\Config\Github\MagentoDockerlabRepo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class EnvironmentDownloadCommand extends Command
{
    const ARG_BRANCH = 'branch';
    const ARG_DESTINATION = 'destination';

    protected function configure()
    {
        $this->addArgument(
            self::ARG_BRANCH,
            InputArgument::OPTIONAL,
            "The branch you'd like to clone.",
            'develop'
        );

        $this->addArgument(
            self::ARG_DESTINATION,
            InputArgument::OPTIONAL,
            "Where you want to clone the project.",
            '.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $branchName = $input->getArgument(self::ARG_BRANCH);
        $destinationPath = $input->getArgument(self::ARG_DESTINATION);
        $repoSshUrl = MagentoDockerlabRepo::getRepoSshUrl();
        $realPath = realpath($destinationPath);

        if (!is_writeable($realPath)) {
            $output->writeln("The destination provided is not writeable. Please check and run the comment again.");
        }

        $process = new Process(['git', 'clone', '--branch', $branchName, $repoSshUrl, $destinationPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output->writeln("Your project was cloned to the following directory: {$realPath}");
        $output->writeln("Using the branch: {$branchName}");
        return Command::SUCCESS;
    }
}

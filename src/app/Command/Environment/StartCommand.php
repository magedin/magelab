<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\Config\Github\MagentoDockerlabRepo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StartCommand extends Command
{
    const ARG_BRANCH = 'branch';
    const ARG_BRANCH_VALUE = 'master';
    const ARG_DESTINATION = 'destination';
    const ARG_GIT = 'git';

    protected function configure()
    {
        $this->addOption(
            self::ARG_BRANCH,
            'b',
            InputOption::VALUE_OPTIONAL,
            "The branch you'd like to clone.",
            self::ARG_BRANCH_VALUE
        );

        $this->addOption(
            self::ARG_DESTINATION,
            'd',
            InputOption::VALUE_OPTIONAL,
            "Where you want to clone the project.",
            '.'
        );

        $this->addOption(
            self::ARG_GIT,
            'g',
            InputOption::VALUE_NONE,
            "Whether you want to keep the git repository references.",
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $branch = $input->getOption(self::ARG_BRANCH);
        $destinationPath = $input->getOption(self::ARG_DESTINATION);
        $realPath = realpath($destinationPath);

        if (!is_writeable($realPath)) {
            throw new RuntimeException("The destination provided is not writeable. Please check and run the comment again.");
        }

        if (!$this->branchExists($branch)) {
            throw new InvalidOptionException("The branch '{$branch}' to clone does not exist.");
        }

        $process = new Process(['git', 'clone', '--branch', $branch, $this->getRepoSshUrl(), $destinationPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output->writeln("Your project was cloned to the following directory: {$realPath}");
        $output->writeln("Using the branch: {$branch}");
        return Command::SUCCESS;
    }
}

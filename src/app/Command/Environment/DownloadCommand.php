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

class DownloadCommand extends Command
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

        if (!$input->getOption(self::ARG_GIT)) {
            $this->cleanGitReferences($realPath);
        }
        return Command::SUCCESS;
    }

    /**
     * @param string $realPath
     * @return void
     */
    private function cleanGitReferences(string $realPath): void
    {
        $refs = ['.git', '.gitignore', '.gitattributes', '.travis.yml'];
        foreach ($refs as $ref) {
            $cleanPath = "{$realPath}/{$ref}";
            $process = new Process(['rm', '-rf', $cleanPath]);
            $process->run();
        }
    }

    /**
     * @param string $branch
     * @return bool
     */
    private function branchExists(string $branch): bool
    {
        if ($branch === self::ARG_BRANCH_VALUE) {
            return true;
        }

        $process = new Process(['git', 'ls-remote', '--heads', $this->getRepoSshUrl(), $branch]);
        $process->run();

        if (!$process->isSuccessful()) {
            return false;
        }

        $output = $process->getOutput();
        if (empty($output)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    private function getRepoSshUrl(): string
    {
        return MagentoDockerlabRepo::getRepoSshUrl();
    }
}
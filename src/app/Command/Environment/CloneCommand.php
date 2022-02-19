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
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\Github\MagentoDockerlabRepo;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CloneCommand extends Command
{
    const ARG_BRANCH = 'branch';
    const ARG_BRANCH_VALUE = 'master';
    const ARG_DESTINATION = 'destination';
    const ARG_GIT = 'git';

    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var DirList
     */
    private DirList $dirList;

    public function __construct(
        Filesystem $filesystem,
        DirList $dirList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
    }

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
        $realPath = $this->prepareDestination($input);

        if (!$this->branchExists($branch)) {
            throw new InvalidOptionException("The branch '$branch' to clone does not exist.");
        }

        $process = Process::run(['git', 'clone', '--branch', $branch, $this->getRepoSshUrl(), $destinationPath]);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->dirList->init();
        $output->writeln("Your project was cloned to the following directory: $realPath");
        $output->writeln("Using the branch: $branch");

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
        $files = ['.git', '.gitignore', '.gitattributes', '.travis.yml'];
        $refs = array_map(function ($file) use ($realPath) {
            return "$realPath/$file";
        }, $files);
        $this->filesystem->remove($refs);
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

        $command = ['git', 'ls-remote', '--heads', $this->getRepoSshUrl(), $branch];
        $process = Process::run($command);
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

    /**
     * @param InputInterface $input
     * @return string
     */
    private function prepareDestination(InputInterface $input): string
    {
        $destinationPath = $input->getOption(self::ARG_DESTINATION);
        $realPath = realpath($destinationPath);

        /** The destination does not exist. */
        if (false === $realPath) {
            $process = Process::run(['mkdir', $destinationPath]);
            if (!$process->isSuccessful()) {
                throw new RuntimeException("the destination provided does not exist and could not be created.");
            }
            $realPath = realpath($destinationPath);
        }

        if (!$realPath || !is_writeable($realPath)) {
            throw new RuntimeException(
                "The destination provided is not writeable. Please check and run the comment again."
            );
        }

        return $realPath;
    }
}

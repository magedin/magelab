<?php

declare(strict_types=1);

namespace MageLab\Command\Environment;

use MageLab\Config\DockerLab\BasePath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class EnvFileCreateCommand extends Command
{
    const ARG_SILENT = 'silent';

    protected function configure()
    {
        $this->addOption(
            self::ARG_SILENT,
            's',
            InputOption::VALUE_NONE,
            "Silently exists if an error happens.",
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $silent = $input->getOption('silent');
        $basePath = BasePath::getRootDir($silent);
        $envFilePath = realpath($basePath) . '/.env';
        $envFile = realpath($envFilePath);

        $filesystem = new Filesystem();
        if ($filesystem->exists($envFile)) {
            if (!$silent) {
                throw new RuntimeException("The environment file already exists.");
            }
            return Command::FAILURE;
        }

        $filesystem->touch($envFilePath);
        return Command::SUCCESS;
    }
}

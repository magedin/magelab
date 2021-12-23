<?php

declare(strict_types=1);

namespace MageLab\Command;

use MageLab\Config\DockerLab\BasePath;
use MageLab\Config\Github\MagentoDockerlabRepo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DockerComposeCommand extends Command
{
    protected function configure()
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        $command = ['docker-compose'];
        array_map(function ($file) use ($rootDir, &$command) {
            $command[] = '-f';
            $command[] = $rootDir . DIRECTORY_SEPARATOR . $file;
        }, $this->getComposeFiles());

        $process = new Process($command);
        $process->run();
        return Command::SUCCESS;
    }

    private function getComposeFiles(): array
    {
        $rootDir = BasePath::getAbsoluteRootDir();
        $process = new Process(['uname']);
        $process->run();
        $operatingSystem = trim($process->getOutput());

        $files = ['docker-compose.yml'];
        if ('Darwin' == $operatingSystem) {
            $files[] = 'docker-compose.dev.mac.yml';
        } else {
            $files[] = 'docker-compose.dev.yml';
        }

        $dotEnv = new Dotenv();
        $dotEnv->loadEnv($rootDir . '/.env');

        $filesystem = new Filesystem();

        $file = 'docker-compose.kibana.yml';
        if ($filesystem->exists($rootDir . '/' . $file) && $_ENV['SERVICE_KIBANA_ENABLED']) {
            $files[] = $file;
        }

        $file = 'docker-compose.mailhog.yml';
        // if ($filesystem->exists($rootDir . '/' . $file) && $_ENV['SERVICE_MAILHOG_ENABLED']) {
            $files[] = $file;
        // }

        $file = 'docker-compose.jenkins.yml';
        if ($filesystem->exists($rootDir . '/' . $file) && $_ENV['SERVICE_JENKINS_ENABLED']) {
            $files[] = $file;
        }

        $file = 'docker-compose.pmm.yml';
        if ($filesystem->exists($rootDir . '/' . $file) && $_ENV['SERVICE_PMM_ENABLED']) {
            $files[] = $file;
        }

        $file = 'docker-compose.custom.yml';
        if ($filesystem->exists($rootDir . '/' . $file)) {
            $files[] = $file;
        }

        return $files;
    }
}

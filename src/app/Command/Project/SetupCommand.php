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

namespace MagedIn\Lab\Command\Project;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\CommandExecutor\Container\Copy;
use MagedIn\Lab\CommandExecutor\Environment\Start;
use MagedIn\Lab\CommandExecutor\Magento\Download;
use MagedIn\Lab\CommandExecutor\Magento\FixOwns;
use MagedIn\Lab\CommandExecutor\Magento\Install;
use MagedIn\Lab\CommandExecutor\Php\Composer;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    /**
     * @var Download
     */
    private Download $magentoDownloadExecutor;

    /**
     * @var Copy
     */
    private Copy $copyExecutor;

    /**
     * @var FixOwns
     */
    private FixOwns $fixOwnsExecutor;

    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    /**
     * @var Start
     */
    private Start $envStartExecutor;

    /**
     * @var Composer
     */
    private Composer $composerExecutor;

    /**
     * @var Install
     */
    private Install $installExecutor;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var Install\DefaultOptions
     */
    private Install\DefaultOptions $installationDefaultOptions;
    /**
     * @var string|null
     */
    private string $downloadedFile = '';

    /**
     * @param Download $magentoDownloadExecutor
     * @param Copy $copyExecutor
     * @param FixOwns $fixOwnsExecutor
     * @param DockerComposeExec $dockerComposeExecCommandBuilder
     * @param Start $envStartExecutor
     * @param Composer $composerExecutor
     * @param Install $installExecutor
     * @param DirList $dirList
     * @param Install\DefaultOptions $installationDefaultOptions
     * @param string|null $name
     */
    public function __construct(
        Download $magentoDownloadExecutor,
        Copy $copyExecutor,
        FixOwns $fixOwnsExecutor,
        DockerComposeExec $dockerComposeExecCommandBuilder,
        Start $envStartExecutor,
        Composer $composerExecutor,
        Install $installExecutor,
        DirList $dirList,
        Install\DefaultOptions $installationDefaultOptions,
        string $name = null
    ) {
        $this->magentoDownloadExecutor = $magentoDownloadExecutor;
        $this->copyExecutor = $copyExecutor;
        $this->fixOwnsExecutor = $fixOwnsExecutor;
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
        $this->envStartExecutor = $envStartExecutor;
        $this->composerExecutor = $composerExecutor;
        $this->installExecutor = $installExecutor;
        $this->dirList = $dirList;
        $this->installationDefaultOptions = $installationDefaultOptions;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The Magento version you want to install'
        );

        $this->addOption(
            'base_url',
            'u',
            InputOption::VALUE_OPTIONAL,
            'The base URL Magento 2 will use.',
            $this->installationDefaultOptions->getDefaultInstallBaseUrl(),
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->startContainers($input, $output);
        $this->prepareApplicationCode($input, $output);
        $this->setupVhost($input, $output);
        $this->installApplication($input, $output);
        $this->syncDirectories($input, $output);
        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function prepareApplicationCode(InputInterface $input, OutputInterface $output)
    {
        $this->downloadedFile = $this->prepareApplicationDownload($input, $output);
        $this->prepareApplicationClean($output);
        $this->prepareApplicationFixOwns();
        $this->prepareApplicationCopy($output);
        $this->prepareApplicationExtract($output);
        $this->prepareApplicationWrapUp();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     */
    private function prepareApplicationDownload(InputInterface $input, OutputInterface $output): string
    {
        $output->writeln('Downloading the application code...');
        return $this->magentoDownloadExecutor->execute([], [
            'version' => $input->getArgument('version'),
            'path' => $this->dirList->getVarDownloadDir(),
            'output' => $output,
        ]);
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    private function prepareApplicationClean(OutputInterface $output): void
    {
        $workingDir = $this->getContainerWorkingDir();
        $output->writeln('Cleaning the working directory inside the container before extracting Magento...');
        $subCommand = ['php', 'rm', '-rf', "$workingDir/", "$workingDir/app/"];
        $deleteCommand = $this->dockerComposeExecCommandBuilder->build($subCommand);
        $process = Process::run($deleteCommand);
    }

    /**
     * @return void
     */
    private function prepareApplicationFixOwns(): void
    {
        $this->fixOwnsExecutor->execute();
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    private function prepareApplicationCopy(OutputInterface $output): void
    {
        $output->writeln('Copying Magento to the PHP container...');
        $this->copyExecutor->execute([], [
            'origin' => $this->downloadedFile,
            'destination' => "php:{$this->getContainerDestination()}",
        ]);
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    private function prepareApplicationExtract(OutputInterface $output): void
    {
        $destination = $this->getContainerDestination();
        $workingDir = $this->getContainerWorkingDir();
        $output->writeln('Extracting Magento into the PHP container...');
        $subCommand = ['php', 'tar', '-xzf', $destination, '--strip-components=1', '--directory', $workingDir];
        $extractCommand = $this->dockerComposeExecCommandBuilder->build($subCommand);
        $process = Process::run($extractCommand);
    }

    /**
     * @return void
     */
    private function prepareApplicationWrapUp(): void
    {
        $subCommand = ['php', 'rm', '-rf', $this->getContainerDestination()];
        $deleteCommand = $this->dockerComposeExecCommandBuilder->build($subCommand);
        $process = Process::run($deleteCommand);
        $process = Process::run(['rm', '-rf', $this->downloadedFile]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function startContainers(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting the services...');
        $this->envStartExecutor->execute();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function setupVhost(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Setting up hosts...');
        /** @todo Setup Host code goes here. */
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function syncDirectories(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Syncing directories between the container and the host...');
        /** @todo Setup Host code goes here. */
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function installApplication(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running composer installation...');
        $this->composerExecutor->execute(['install', '--no-interaction']);
        $this->installMagento($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function installMagento(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Installing Magento application...');
        $baseUrl = $input->getOption('base_url');
        $this->installExecutor->execute([], ['base_url' => $baseUrl]);
    }

    /**
     * @return string
     */
    private function getContainerWorkingDir(): string
    {
        return '/var/www/html';
    }

    /**
     * @return string
     */
    private function getContainerFilename(): string
    {
        return 'magento.tar.gz';
    }

    /**
     * @return string
     */
    private function getContainerDestination(): string
    {
        return "{$this->getContainerWorkingDir()}/{$this->getContainerFilename()}";
    }
}

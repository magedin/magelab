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
use MagedIn\Lab\CommandExecutor\Environment\Start;
use MagedIn\Lab\CommandExecutor\Magento\Download;
use MagedIn\Lab\CommandExecutor\Magento\Install;
use MagedIn\Lab\CommandExecutor\Php\Composer;
use MagedIn\Lab\Config;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileWriter;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;
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

    public function __construct(
        Download $magentoDownloadExecutor,
        Start $envStartExecutor,
        Composer $composerExecutor,
        Install $installExecutor,
        DirList $dirList,
        Install\DefaultOptions $installationDefaultOptions,
        string $name = null
    ) {
        $this->magentoDownloadExecutor = $magentoDownloadExecutor;
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
        $this->prepareApplicationCode($input, $output);
        $this->setupVhost($input, $output);
        $this->startContainers($input, $output);
        $this->installApplication($input, $output);
        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function prepareApplicationCode(InputInterface $input, OutputInterface $output)
    {
        $downloadedFile = $this->magentoDownloadExecutor->execute([], [
            'version' => $input->getArgument('version'),
            'path' => $this->dirList->getSrcDir(),
            'output' => $output,
        ]);

        $output->writeln('Extracting the application code...');
        $command = ['tar', '-xzf', $downloadedFile, '--strip-components=1', '--directory', $this->dirList->getSrcDir()];
        Process::run($command);
        Process::run(['rm', '-rf', $downloadedFile]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function startContainers(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting the services.');
        $this->envStartExecutor->execute();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function setupVhost(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Setting up hosts.');
        /** @todo Setup Host code goes here. */
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function installApplication(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running composer installation.');
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
        $output->writeln('Installing Magento application.');
        $baseUrl = $input->getOption('base_url');
        $this->installExecutor->execute([], ['base_url' => $baseUrl]);
    }
}

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
use MagedIn\Lab\CommandExecutor\Php\Composer;
use MagedIn\Lab\Config;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileWriter;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * @var DirList
     */
    private DirList $dirList;

    public function __construct(
        Download $magentoDownloadExecutor,
        Start $envStartExecutor,
        Composer $composerExecutor,
        DirList $dirList,
        string $name = null
    ) {
        $this->magentoDownloadExecutor = $magentoDownloadExecutor;
        $this->envStartExecutor = $envStartExecutor;
        $this->composerExecutor = $composerExecutor;
        $this->dirList = $dirList;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The Magento version you want to install'
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
        $this->setupHosts($input, $output);
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
    private function setupHost(InputInterface $input, OutputInterface $output)
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
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function installMagento(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Installing Magento application.');
        /** @todo Install Magento application. */
    }
}

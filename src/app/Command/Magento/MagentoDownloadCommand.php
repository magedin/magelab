<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\Magento\Download;
use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoDownloadCommand extends Command
{
    const ARG_VERSION = 'version';
    const OPTION_FORCE = 'force';

    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    /**
     * @var bool
     */
    protected bool $requiresDocker = false;

    /**
     * @var Download
     */
    private Download $commandExecutor;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @param Download $commandExecutor
     * @param DirList $dirList
     * @param string|null $name
     */
    public function __construct(
        Download $commandExecutor,
        DirList $dirList,
        string $name = null
    ) {
        $this->commandExecutor = $commandExecutor;
        $this->dirList = $dirList;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            self::ARG_VERSION,
            InputArgument::OPTIONAL,
            'Magento 2 Version',
            'latest'
        );
        $this->addOption(
            self::OPTION_FORCE,
            'f',
            InputOption::VALUE_NEGATABLE,
            'For the download even if the Magento version is already downloaded.',
            false
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = $input->getArgument(self::ARG_VERSION);
        $config = [
            'version' => $version,
            'force' => $input->getOption(self::OPTION_FORCE),
            'path' => $this->dirList->getMagentoDownloadDir(),
            'output' => $output
        ];
        $this->commandExecutor->execute([], $config);
        return Command::SUCCESS;
    }
}

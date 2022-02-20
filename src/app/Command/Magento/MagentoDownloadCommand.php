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

namespace MagedIn\Lab\Command\Magento;

use GuzzleHttp\Client as HttpClient;
use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandExecutor\Magento\Download;
use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class MagentoDownloadCommand extends Command
{
    const ARG_VERSION = 'version';
    const OPTION_PATH = 'path';

    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    /**
     * @var Download
     */
    private Download $commandExecutor;

    public function __construct(
        Download $commandExecutor,
        string $name = null
    ) {
        $this->commandExecutor = $commandExecutor;
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
            self::OPTION_PATH,
            'p',
            InputOption::VALUE_OPTIONAL,
            'The path on your machine where Magento 2 copy will be downloaded to.',
            realpath('.')
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
        $path = $input->getOption(self::OPTION_PATH);
        if (realpath($path) === false) {
            throw new InvalidOptionException('This path does not exist or is invalid.');
        }

        $config = [
            'version' => $version,
            'path' => $path,
            'output' => $output
        ];
        $this->commandExecutor->execute([], $config);
        return Command::SUCCESS;
    }
}

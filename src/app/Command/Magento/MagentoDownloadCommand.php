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

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MagedIn\Lab\Command\Command;
use MagedIn\Lab\Helper\Github\DownloadRepo;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class MagentoDownloadCommand extends Command
{
    const ARG_VERSION = 'version';
    const ARG_PATH = 'path';

    const FILE_EXTENSION = '.tar.gz';

    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    public function __construct(
        Filesystem $filesystem,
        HttpClient $httpClient,
        string $name = null
    ) {
        parent::__construct($name);
        $this->filesystem = $filesystem;
        $this->httpClient = $httpClient;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            self::ARG_VERSION,
            InputArgument::OPTIONAL,
            'Magento Version',
            'latest'
        );

        $this->addArgument(
            self::ARG_PATH,
            InputArgument::OPTIONAL,
            'The path where Magento will be downloaded to.',
            getcwd()
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = $input->getArgument(self::ARG_VERSION);
        $this->checkIfVersionExists($version);

        $path = $input->getArgument(self::ARG_PATH);
        $filepath = "$path/" . $this->getFilename($version);

        if ($this->filesystem->exists($filepath)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                "There is already a file on $filepath. Do you want to replace it (y/n)?",
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln("Ok. Nothing will be downloaded at this moment.");
                return Command::FAILURE;
            }
            $this->filesystem->remove($filepath);
        }

        $output->writeln("Your Magento copy will be downloaded to: $filepath.");
        $progressBar = $this->createProgressBar($output);
        $this->performDownload($version, $filepath, $progressBar);
        $output->writeln('');
        $output->writeln('Your file was successfully downloaded!');
        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return ProgressBar
     */
    private function createProgressBar(OutputInterface $output): ProgressBar
    {
        $progressBar = new ProgressBar($output);
        $progressBar->setFormat('debug');
        $progressBar->setRedrawFrequency(1);
        return $progressBar;
    }

    /**
     * @param string $version
     * @param string $filepath
     * @param ProgressBar $progressBar
     * @return void
     * @throws GuzzleException
     */
    private function performDownload(string $version, string $filepath, ProgressBar $progressBar)
    {
        $progress = function ($downloadTotal, $downloadedBytes) use ($progressBar) {
            $progressBar->setMaxSteps($downloadTotal);
            $progressBar->setProgress($downloadedBytes);
        };

        $options = [
            RequestOptions::SINK => $filepath,
            RequestOptions::PROGRESS => $progress,
        ];
        $progressBar->start();
        $this->httpClient->get($this->getDownloadUrl($version), $options);
        $progressBar->finish();
    }

    /**
     * @param string $version
     * @return void
     * @throws GuzzleException
     * @throws Exception
     */
    private function checkIfVersionExists(string $version): void
    {
        try {
            $this->httpClient->head($this->getDownloadUrl($version), ['timeout' => 1]);
        } catch (Exception $e) {
            if (404 === $e->getCode()) {
                throw new Exception('This version of Magento does not exist. Please try another one.');
            }
        }
    }

    /**
     * @param string $version
     * @return string
     */
    private function getDownloadUrl(string $version): string
    {
        return DownloadRepo::buildDownloadUrl($this->getFilename($version));
    }

    /**
     * @param string $version
     * @return string
     */
    private function getFilename(string $version): string
    {
        return "$version" . self::FILE_EXTENSION;
    }
}

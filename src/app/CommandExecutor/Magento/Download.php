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

namespace MagedIn\Lab\CommandExecutor\Magento;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Helper\Github\DownloadRepo;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Download extends CommandExecutorAbstract
{
    const FILE_EXTENSION = '.tar.gz';

    /**
     * @var array|array[]
     */
    protected array $config = [
        'version' => null,
        'path' => null,
        'output' => null
    ];

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
        HttpClient $httpClient
    ) {
        $this->filesystem = $filesystem;
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritDoc
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $version = $config['version'];
        $path = $config['path'];
        $output = $config['output'];
        $filepath = "$path" . DS . $this->getFilename($version);

        if ($this->filesystem->exists($filepath)) {
            $this->filesystem->remove($filepath);
        }

        $this->checkIfVersionExists($version);

        $output->writeln("Your Magento copy will be downloaded to: $filepath.");
        $progressBar = $this->createProgressBar($output);
        $progressBar->setMessage("Your Magento copy will be downloaded to: $filepath.");
        $this->performDownload($version, $filepath, $progressBar);
        $output->writeln('');
        $output->writeln('Your file was successfully downloaded!');
        return $filepath;
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
        $progressBar->setBarWidth(2000);
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
        $downloadUrl = $this->getRealDownloadUrl($version);
        $contentLength = $this->getContentLength($downloadUrl, $progressBar);

        $progress = function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($progressBar) {
            $progressBar->setProgress($downloadedBytes);
        };

        $options = [
            RequestOptions::SINK => $filepath,
            RequestOptions::PROGRESS => $progress,
        ];

        $progressBar->setMaxSteps($contentLength);
        $progressBar->start();
        $this->httpClient->get($downloadUrl, $options);
        $progressBar->finish();
    }

    /**
     * @param string $downloadUrl
     * @return int
     * @throws GuzzleException
     */
    private function getContentLength(string $downloadUrl, ProgressBar $progressBar): int
    {
        try {
            $length = 0;
            $this->httpClient->get($downloadUrl, [
                RequestOptions::ON_HEADERS => function (ResponseInterface $response) use (&$length, $progressBar) {
                    $length = (int) $response->getHeaderLine('Content-Length');
                    $progressBar->setMaxSteps($length);
                    throw new \Exception('Max steps is set.');
                }
            ]);
        } catch (\Exception $e) {}
        return $length;
    }

    /**
     * @param string $version
     * @return string|null
     * @throws GuzzleException
     */
    private function getRealDownloadUrl(string $version): ?string
    {
        $response = $this->httpClient->get($this->getDownloadUrl($version), [
            RequestOptions::ALLOW_REDIRECTS => false
        ]);
        $downloadUrl = $response->getHeaderLine('Location');
        if (empty($downloadUrl)) {
            return null;
        }
        return $downloadUrl;
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

    /**
     * @param string $version
     * @return void
     * @throws GuzzleException
     * @throws \Exception
     */
    private function checkIfVersionExists(string $version): void
    {
        try {
            $this->httpClient->head($this->getDownloadUrl($version), ['timeout' => 2]);
        } catch (\Exception $e) {
            if (404 === $e->getCode()) {
                throw new InvalidArgumentException('This version of Magento does not exist. Please try another one.');
            }
        }
    }
}

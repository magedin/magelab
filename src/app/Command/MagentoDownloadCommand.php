<?php

declare(strict_types=1);

namespace MageLab\Command;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoDownloadCommand extends Command
{
    const ARG_VERSION = 'version';
    const ARG_PATH = 'path';

    const REPO_BASE_URL = 'https://github.com/magedin/magento-opensource-releases/archive/';
    const FILE_EXTENSION = '.tar.gz';

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
        $filepath = "$path/".$this->getFilename($version);

        if (realpath($filepath)) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                "There is already a file on $filepath. Do you want to replace it (y/n)?",
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln("Ok. Nothing will be downloaded at this moment.");
                return Command::FAILURE;
            }
            unlink($filepath);
        }

        $output->writeln("Your Magento copy will be downloaded to: $filepath.");
        $this->performDownload($version, $filepath);
        $output->writeln('Your file was successfully downloaded!');
        return Command::SUCCESS;
    }

    /**
     * @param string $version
     * @param string $filepath
     * @return void
     * @throws GuzzleException
     */
    private function performDownload(string $version, string $filepath)
    {
        $options = [
            RequestOptions::SINK => $filepath
        ];
        (new HttpClient())->get($this->buildDownloadUrl($version), $options);
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
            (new HttpClient())->head($this->buildDownloadUrl($version), ['timeout' => 1]);
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
    private function buildDownloadUrl(string $version): string
    {
        return self::REPO_BASE_URL . $this->getFilename($version);
    }

    /**
     * @param string $version
     * @return string
     */
    private function getFilename(string $version): string
    {
        return "{$version}" . self::FILE_EXTENSION;
    }
}

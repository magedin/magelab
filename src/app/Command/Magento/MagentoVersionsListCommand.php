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

use MagedIn\Lab\Command\Command;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use MagedIn\Lab\Helper\Github\DownloadRepo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoVersionsListCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $versions = $this->getVersions();
        arsort($versions);

        $output->writeln("Check the available versions on our repo:\n");

        array_map(function ($version) use ($output) {
            $output->writeln($version);
        }, $versions);

        return Command::SUCCESS;
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    private function getVersions(): array
    {
        $versions = [];
        array_map(function (array $tag) use (&$versions) {
            $versions[] = $tag['name'];
        }, $this->getTags() ?? []);
        return $versions;
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    private function getTags(): array
    {
        $page = 1;
        $max = 100;
        $allTags = [];

        do {
            $currentSet = $this->requestTags($max, $page);
            $allTags = array_merge($allTags, $currentSet);
            if (count($currentSet) < $max) {
                break;
            }
            $page++;
        } while (count($currentSet) <= $max);
        return $allTags;
    }

    /**
     * @param int $max
     * @param int $page
     * @return array
     * @throws GuzzleException
     */
    private function requestTags(int $max, int $page = 1): array
    {
        $uri = DownloadRepo::getRepoTagsUrl(['per_page' => $max, 'page' => $page]);
        $client = new HttpClient();
        $response = $client->get($uri, ['timeout' => DownloadRepo::getRequestDefaultTimeout()]);
        $json = (string) $response->getBody();
        return (array) json_decode($json, true);
    }
}

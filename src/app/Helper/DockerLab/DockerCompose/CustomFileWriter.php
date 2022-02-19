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

namespace MagedIn\Lab\Helper\DockerLab\DockerCompose;

use MagedIn\Lab\Config;
use MagedIn\Lab\Helper\Config\ConfigMerger;
use MagedIn\Lab\Helper\Config\ConfigParser;
use MagedIn\Lab\Helper\Config\ConfigWriter;
use MagedIn\Lab\Helper\DockerIp;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\Installation;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;
use MagedIn\Lab\Model\Config\Services;
use Symfony\Component\Filesystem\Filesystem;

class CustomFileWriter
{
    /**
     * @var ConfigMerger
     */
    private ConfigMerger $configMerger;

    /**
     * @var ConfigWriter
     */
    private ConfigWriter $configWriter;

    /**
     * @var ConfigParser
     */
    private ConfigParser $configParser;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var array
     */
    private array $defaultConfig = [
        'version' => "3.5",
        'services' => [
            'php' => [],
            'nginx' => [],
            'db' => [],
            'redis' => [],
            'elasticsearch' => [],
        ],
    ];

    /**
     * @var Services
     */
    private Services $services;

    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    /**
     * @var Installation
     */
    private Installation $installation;

    /**
     * @var DockerIp
     */
    private DockerIp $dockerIp;

    /**
     * @var Writer
     */
    private Writer $localConfigWriter;

    public function __construct(
        ConfigMerger $configMerger,
        ConfigWriter $configWriter,
        ConfigParser $configParser,
        Filesystem $filesystem,
        DirList $dirList,
        Services $services,
        OperatingSystem $operatingSystem,
        Installation $installation,
        DockerIp $dockerIp,
        Writer $localConfigWriter
    ) {
        $this->configMerger = $configMerger;
        $this->configWriter = $configWriter;
        $this->configParser = $configParser;
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
        $this->services = $services;
        $this->operatingSystem = $operatingSystem;
        $this->installation = $installation;
        $this->dockerIp = $dockerIp;
        $this->localConfigWriter = $localConfigWriter;
    }

    private function prepareDefaultConfig()
    {
        /**
         * Linux only: host.docker.internal doesn't exist https://github.com/docker/for-linux/issues/264
         * result of: docker run --rm alpine ip route | awk 'NR==1 {print $3}'
         */
        if (!$this->operatingSystem->isLinux()) {
            return;
        }

        /** Format $dockerIp = "172.17.0.1" */
        $dockerIp = $this->dockerIp->get();
        $this->defaultConfig['services'] = [
            'php' => [
                'extra_hosts' => [
                    "host.docker.internal:$dockerIp",
                ]
            ],
        ];
    }

    /**
     * @param array $config
     * @return array
     */
    private function rebuildContainerNames(array $config): array
    {
        $defaultServices = ['php', 'nginx', 'db', 'redis', 'elasticsearch'];
        $optionalServices = $this->services->getEnabledServices();
        $services = array_merge($defaultServices, $optionalServices);

        foreach ($services as $service) {
            $config['services'][$service]['container_name'] = $this->buildContainerName($service);
        }
        return $config;
    }

    /**
     * @param array $config
     * @return void
     */
    public function write(array $config = []): void
    {
        if (!$this->installation->isInstalled()) {
            return;
        }
        $this->prepareDefaultConfig();
        $finalConfig = $this->defaultConfig;
        if ($this->filesystem->exists($this->getConfigFilename())) {
            $fileConfig = $this->loadCurrentContent();
            $this->configMerger->merge($fileConfig, $finalConfig);
        }

        $this->configMerger->merge($config, $finalConfig);
        $finalConfig = $this->rebuildContainerNames($finalConfig);
        $this->configWriter->write($this->getConfigFilename(), $finalConfig);
    }

    /**
     * @return string
     */
    public function getConfigFilename(): string
    {
        return $this->dirList->getRootDir() . DS . 'docker-compose.custom.yml';
    }

    /**
     * @return array
     */
    public function loadCurrentContent(): array
    {
        return $this->configParser->parse($this->getConfigFilename());
    }

    /**
     * @param string $service
     * @return string
     */
    private function buildContainerName(string $service): string
    {
        /** Always load the most fresh data from config. */
        $localConfig = $this->localConfigWriter->load();
        $prefix = $localConfig['project']['name'] ?? null;
        if ($prefix) {
            $service = $prefix . '_' . $service;
        }
        return $service;
    }
}

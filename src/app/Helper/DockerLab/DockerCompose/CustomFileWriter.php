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
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Config\Services;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class CustomFileWriter
{
    /**
     * @var ConfigMerger
     */
    private ConfigMerger $configMerger;

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

    public function __construct(
        ConfigMerger $configMerger,
        Filesystem $filesystem,
        DirList $dirList,
        Services $services,
        OperatingSystem $operatingSystem
    ) {
        $this->configMerger = $configMerger;
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
        $this->services = $services;
        $this->operatingSystem = $operatingSystem;
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

        $dockerIp = "172.17.0.1" /** @todo Change this to make it a dynamic value. */;
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
        $this->prepareDefaultConfig();
        $finalConfig = $this->defaultConfig;
        if ($this->filesystem->exists($this->getConfigFilename())) {
            $fileConfig = Yaml::parse(file_get_contents($this->getConfigFilename()));
            $this->configMerger->merge($fileConfig, $finalConfig);
        }

        $this->configMerger->merge($config, $finalConfig);
        $finalConfig = $this->rebuildContainerNames($finalConfig);
        $yaml = Yaml::dump($finalConfig, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $this->filesystem->dumpFile($this->getConfigFilename(), $yaml);
    }

    /**
     * @return string
     */
    public function getConfigFilename(): string
    {
        return $this->dirList->getRootDir() . DS . 'docker-compose.custom.yml';
    }

    /**
     * @param string $service
     * @return string
     */
    private function buildContainerName(string $service): string
    {
        $prefix = Config::get('project/name');
        if ($prefix) {
            $service = $prefix . '_' . $service;
        }
        return $service;
    }
}

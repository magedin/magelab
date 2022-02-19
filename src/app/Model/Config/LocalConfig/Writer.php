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

namespace MagedIn\Lab\Model\Config\LocalConfig;

use MagedIn\Lab\Helper\Config\ConfigMerger;
use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Writer
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

    public function __construct(
        ConfigMerger $configMerger,
        Filesystem $filesystem,
        DirList $dirList
    ) {
        $this->configMerger = $configMerger;
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
    }

    /**
     * @var array
     */
    private array $defaultConfig = [
        'project' => [
            'name' => null,
        ],
        'services' => [
            'mailhog' => [
                'enabled' => true,
            ],
            'kibana' => [
                'enabled' => false,
            ],
            'rabbitmq' => [
                'enabled' => false,
            ],
            'varnish' => [
                'enabled' => false,
            ],
        ],
    ];

    /**
     * @param array $config
     * @return void
     */
    public function write(array $config = [])
    {
        $finalConfig = $this->defaultConfig;
        $this->configMerger->merge($config, $finalConfig);
        $yaml = Yaml::dump($finalConfig, 10, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $this->filesystem->dumpFile($this->getConfigFilename(), $yaml);
    }

    /**
     * @return string
     */
    public function getConfigFilename(): string
    {
        return $this->dirList->getVarDir() . DS . 'project.config.yaml';
    }
}

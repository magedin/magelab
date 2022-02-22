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
use MagedIn\Lab\Helper\Config\ConfigParser;
use MagedIn\Lab\Helper\Config\ConfigWriter;
use MagedIn\Lab\Helper\DockerLab\DirList;
use MagedIn\Lab\Helper\DockerLab\Installation;
use MagedIn\Lab\Helper\Generator\Username;
use MagedIn\Lab\Helper\Generator\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class Writer
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
     * @var Installation
     */
    private Installation $installation;

    /**
     * @var Username
     */
    private Username $usernameGenerator;

    /**
     * @var Uuid
     */
    private Uuid $uuidGenerator;

    public function __construct(
        ConfigMerger $configMerger,
        ConfigWriter $configWriter,
        ConfigParser $configParser,
        Filesystem $filesystem,
        DirList $dirList,
        Installation $installation,
        Username $usernameGenerator,
        Uuid $uuidGenerator
    ) {
        $this->configMerger = $configMerger;
        $this->configWriter = $configWriter;
        $this->configParser = $configParser;
        $this->filesystem = $filesystem;
        $this->dirList = $dirList;
        $this->installation = $installation;
        $this->usernameGenerator = $usernameGenerator;
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @var array
     */
    private array $defaultConfig = [
        'project' => [
            'id' => null,
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
            'blackfire' => [
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
        if (!$this->installation->isInstalled()) {
            return;
        }
        $finalConfig = $this->defaultConfig;
        if ($this->configExists()) {
            $currentConfig = $this->load();
            $this->configMerger->merge($currentConfig, $finalConfig);
        }
        $this->configMerger->merge($config, $finalConfig);
        $this->checkProjectName($finalConfig);
        $this->configWriter->write($this->getConfigFilename(), $finalConfig);
    }

    /**
     * @return array
     */
    public function load(): array
    {
        if (!$this->configExists()) {
            return [];
        }
        return $this->configParser->parse($this->getConfigFilename());
    }

    /**
     * @return string
     */
    public function getConfigFilename(): string
    {
        return $this->dirList->getVarDir() . DS . 'project.config.yaml';
    }

    /**
     * @param array $config
     * @return void
     */
    private function checkProjectName(array &$config = [])
    {
        if (empty($config['project']['id'])) {
            $config['project']['id'] = $this->uuidGenerator->generate();
        }
        if (empty($config['project']['name'])) {
            $config['project']['name'] = $this->usernameGenerator->generate();
        }
    }

    /**
     * @return bool
     */
    private function configExists(): bool
    {
        return $this->filesystem->exists($this->getConfigFilename());
    }
}

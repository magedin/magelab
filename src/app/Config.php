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

namespace MagedIn\Lab;

use MagedIn\Lab\Helper\Config\ConfigMerger;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array
     */
    private static array $config = [];

    /**
     * @var Filesystem
     */
    private static Filesystem $filesystem;

    /**
     * @var Writer
     */
    private static Writer $localConfigWriter;

    /**
     * @var ConfigMerger
     */
    private static ConfigMerger $configMerger;

    private static function init()
    {
        self::$filesystem = ObjectManager::getInstance()->get(Filesystem::class);
        self::$localConfigWriter = ObjectManager::getInstance()->get(Writer::class);
        self::$configMerger = ObjectManager::getInstance()->get(ConfigMerger::class);
    }

    /**
     * @param string $index
     * @return array|string|null
     */
    public static function get(string $index)
    {
        self::load();
        return self::getConfigValue($index, self::$config);
    }

    /**
     * @return void
     */
    private static function load(): void
    {
        if (empty(self::$config)) {
            self::init();
            foreach (self::loadConfigFiles() as $file) {
                $config = (array) Yaml::parse($file->getContents());
                self::$configMerger->merge($config, self::$config);
            }
            $localConfig = (array) Yaml::parse(file_get_contents(self::$localConfigWriter->getConfigFilename()));
            self::$configMerger->merge($localConfig, self::$config);
        }
    }

    /**
     * @param string $index
     * @param array $config
     * @return array|string|bool|null
     */
    private static function getConfigValue(string $index, array $config)
    {
        if (strpos($index, '/')) {
            $paths = explode('/', $index);
            foreach ($paths as $path) {
                $config = $config[$path] ?? null;
            }
        } else {
            $config = $config[$index] ?? null;
        }
        return $config;
    }

    /**
     * @return Finder
     */
    private static function loadConfigFiles(): Finder
    {
        self::createLocalConfig();

        /** @var Finder $finder */
        $finder = ObjectManager::getInstance()->get(Finder::class);
        $finder->files()->in(CONFIG_DIR)->name(['*.yaml', '*.yml']);

        if (!$finder->hasResults()) {
            throw new RuntimeException('No configuration file was found.');
        }

        $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
            if (preg_match('/.*\..*\.(yaml|yml)$/', $a->getFilename())) {
                return 999999;
            }
            return null;
        });

        return $finder;
    }

    /**
     * @return void
     */
    private static function createLocalConfig(): void
    {
        if (!self::$filesystem->exists(self::$localConfigWriter->getConfigFilename())) {
            self::$localConfigWriter->write();
        }
    }
}

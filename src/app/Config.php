<?php

declare(strict_types=1);

namespace MagedIn\Lab;

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
     * @return void
     */
    public static function load(): void
    {
        if (empty(self::$config)) {
            foreach (self::loadConfigFiles() as $file) {
                self::$config = array_merge(self::$config, (array) Yaml::parse($file->getContents()));
            }
        }
    }

    /**
     * @param string $index
     * @return mixed|null
     */
    public static function get(string $index)
    {
        self::load();
        if (isset(self::$config[$index])) {
            return self::$config[$index];
        }
        return null;
    }

    /**
     * @return Finder
     */
    private static function loadConfigFiles(): Finder
    {
        /** @var Finder $finder */
        $finder = ObjectManager::getInstance()->get(Finder::class);
        $finder->files()->in(CONFIG_DIR)->name(['*.yaml', '*.yml']);

        if (!$finder->hasResults()) {
            throw new RuntimeException('No configuration file was found.');
        }

        $finder->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
            if (preg_match('/.*\.dev.(yaml|yml)$/', $a->getFilename())) {
                return 999999;
            }
            return null;
        });

        return $finder;
    }
}

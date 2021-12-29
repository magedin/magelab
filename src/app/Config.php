<?php

declare(strict_types=1);

namespace MagedIn\Lab;

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

    public static function load()
    {
        if (empty(self::$config)) {
            foreach (self::loadConfigFiles('*.yaml') as $file) {
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
     * @param string|null $pattern
     * @return Finder
     */
    private static function loadConfigFiles(string $pattern = null): Finder
    {
        /** @var Finder $finder */
        $finder = ObjectManager::getInstance()->get(Finder::class);
        $finder->files()
            ->in(__DIR__ . '/config')
            ->sortByName(true);

        if ($pattern) {
            $finder->name($pattern);
        }

        if (!$finder->hasResults()) {
            throw new RuntimeException('No config files was found.');
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

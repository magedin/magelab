<?php

declare(strict_types=1);

namespace MageLab;

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
            $file = __DIR__ . '/config.yaml';
            $contents = file_get_contents($file);
            self::$config = (array) Yaml::parse($contents);
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
}

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

namespace MagedIn\Lab\Helper\DockerLab;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class BasePath
{
    /**
     * @var int
     */
    private static int $maxLevel = 20;

    /**
     * @var string
     */
    private static string $rootDir = '';

    /**
     * @param bool $silent
     * @return string|null
     */
    public static function getAbsoluteRootDir(bool $silent = false): ?string
    {
        return realpath(self::getRootDir($silent));
    }

    /**
     * @param bool $silent
     * @return string|null
     */
    public static function getRootDir(bool $silent = false): ?string
    {
        if (self::$rootDir) {
            return self::$rootDir;
        }

        $dirs = self::buildDirLevels();
        foreach ($dirs as $currentDir) {
            if (self::isRootDir($currentDir)) {
                self::$rootDir = $currentDir;
                return self::$rootDir;
            }
        }
        if ($silent) {
            return null;
        }
        throw new RuntimeException("You are not under a DockerLab project.");
    }

    /**
     * @param string $dir
     * @return bool
     */
    private static function isRootDir(string $dir): bool
    {
        $verificationFiles = [
            "var/.dockerlab",
            "docker-compose.yml",
            "docker-compose.dev.yml",
            "docker-compose.dev.mac.yml",
            "docker-compose.mailhog.yml",
            "var/template/nginx/upstream.conf",
            "var/template/nginx/magento2.conf",
            "var/template/nginx/magento2-ssl.conf",
        ];

        $realFiles = array_map(function (string $file) use ($dir) {
            return realpath("$dir/$file");
        }, $verificationFiles);

        return (new Filesystem())->exists($realFiles);
    }

    /**
     * @return array
     */
    private static function buildDirLevels(): array
    {
        $dirs = [];

        $current = './';
        $cd = '../';
        for ($y = 0; $y <= self::$maxLevel; $y++) {
            $value = $current;
            for ($x = 1; $x <= $y; $x++) {
                $value .= $cd;
            }
            $dirs[] = $value;
        }
        return $dirs;
    }
}

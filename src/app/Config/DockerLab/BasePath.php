<?php

declare(strict_types=1);

namespace MageLab\Config\DockerLab;

use Symfony\Component\Console\Exception\RuntimeException;

class BasePath
{
    /**
     * @var int
     */
    private static int $maxLevel = 10;

    /**
     * @param bool $silent
     * @return string|null
     */
    public static function getRootDir(bool $silent = false): ?string
    {
        $dirs = self::buildDirLevels();
        foreach ($dirs as $currentDir) {
            if (self::isRootDir($currentDir)) {
                return $currentDir;
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

        foreach ($verificationFiles as $verificationFile) {
            $realFile = realpath("$dir/$verificationFile");
            if (!$realFile || !self::checkFile($realFile)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    private static function checkFile(string $filePath): bool
    {
        if (!$filePath || !file_exists($filePath) && !is_readable($filePath)) {
            return false;
        }
        return true;
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

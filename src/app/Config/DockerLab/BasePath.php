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
            $verificationFile = realpath("$currentDir/var/.dockerlab");
            if ($verificationFile && file_exists($verificationFile) && is_readable($verificationFile)) {
                return realpath($currentDir);
            }
        }
        if ($silent) {
            return null;
        }
        throw new RuntimeException("You are not under a DockerLab project.");
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

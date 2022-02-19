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

namespace MagedIn\Lab\Helper\Config;

use Symfony\Component\Filesystem\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConfigParser
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $filename
     * @return array
     */
    public function parse(string $filename): array
    {
        if (!$this->filesystem->exists($filename)) {
            throw new RuntimeException(sprintf('Config "%s" file does not exist.', $filename));
        }
        return Yaml::parse(file_get_contents($filename));
    }
}

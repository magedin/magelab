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

namespace MagedIn\Lab\Helper\DockerLab\DockerCompose;

use Symfony\Component\Filesystem\Filesystem;

class DockerComposeFileValidator
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
     * @return bool
     */
    public function validate(string $filename): bool
    {
        $absoluteFilename = $filename;
        if (!$this->filesystem->exists($absoluteFilename)) {
            return false;
        }
        if (!is_readable($absoluteFilename)) {
            return false;
        }
        return true;
    }
}

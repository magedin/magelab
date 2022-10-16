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

namespace MagedIn\Lab\Helper\DockerLab;

use MagedIn\Lab\Helper\OperatingSystem;
use Symfony\Component\Filesystem\Filesystem;

class DirList
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var Installation
     */
    private Installation $installation;

    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    public function __construct(
        Filesystem $filesystem,
        Installation $installation,
        OperatingSystem $operatingSystem
    ) {
        $this->filesystem = $filesystem;
        $this->installation = $installation;
        $this->operatingSystem = $operatingSystem;
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->getSrcDir();
    }

    /**
     * @return string
     */
    public function getVarDir(): string
    {
        return $this->getRootDir() . DS . 'var';
    }

    /**
     * @param string|null $group
     * @return string
     */
    public function getTemplateDir(string $group = null): string
    {
        $dir = $this->getVarDir() . DS . 'template';
        $proposed = $dir . DS . $group;
        if (!empty($group) && realpath($proposed)) {
            return $proposed;
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function getNginxTemplateDir(): string
    {
        return $this->getTemplateDir('nginx');
    }

    /**
     * @return string
     */
    public function getConfigDir(): string
    {
        return $this->getRootDir() . DS . 'config';
    }

    /**
     * @param string|null $domain
     * @param bool $autoCreate
     * @return string
     */
    public function getNginxConfigDir(string $domain = null, bool $autoCreate = true): string
    {
        $dir = $this->getConfigDir() . DS . 'nginx' . DS . 'conf.d';
        if (!empty($domain)) {
            $dir .= DS . $domain;
            if ($autoCreate && !$this->filesystem->exists($dir)) {
                $this->filesystem->mkdir($dir);
            }
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function getVarDownloadDir(): string
    {
        $dir = $this->getVarDir() . DS . 'download';
        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return realpath(BasePath::getRootDir(true));
    }

    /**
     * @return string
     */
    public function getSrcDir(): ?string
    {
        if (!$this->installation->isInstalled()) {
            return null;
        }
        $srcDir = $this->getRootDir() . DS . 'src';
        $this->filesystem->mkdir($srcDir);
        return $srcDir;
    }

    /**
     * @param string $path
     * @return string
     */
    public function absolutePathFromRoot(string $path): string
    {
        $path = ltrim($path, DS);
        return $this->getRootDir() . DS . $path;
    }

    /**
     * @return string
     */
    public function getMagelabHomeUserDir(): string
    {
        $homeDir = $this->getUserHomeDir() . DS . '.magelab';
        if ($homeDir && !$this->filesystem->exists($homeDir)) {
            $this->filesystem->mkdir($homeDir);
        }
        return $homeDir;
    }

    /**
     * @return string
     */
    public function getUserHomeDir(): string
    {
        if ($this->operatingSystem->isWindows()) {
            $home = $_SERVER['HOMEDRIVE'] . DS . $_SERVER['HOMEPATH'] ?? '';
        } else {
            $home = $_SERVER['HOME'] ?? '';
        }
        return realpath($home);
    }

    /**
     * @return string
     */
    public function getMagentoDownloadDir(): string
    {
        $dir = $this->getMagelabHomeUserDir() . DS . 'downloads';
        if ($dir && !$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir);
        }
        return $dir;
    }
}

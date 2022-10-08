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

    public function __construct(
        Filesystem $filesystem,
        Installation $installation
    ) {
        $this->filesystem = $filesystem;
        $this->installation = $installation;
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
}

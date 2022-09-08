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

namespace MagedIn\Lab\Helper\Magento;

use MagedIn\Lab\Helper\DockerLab\DirList;

class MagentoDirList
{
    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @param DirList $dirList
     */
    public function __construct(
        DirList $dirList
    ) {
        $this->dirList = $dirList;
    }

    /**
     * @return string
     */
    public function getAppConfigDir(): string
    {
        return $this->getMagentoRootDir() . DS . 'app/etc/';
    }

    /**
     * @return string
     */
    private function getMagentoRootDir(): string
    {
        return $this->dirList->getSrcDir();
    }
}

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

namespace MagedIn\Lab\Helper\User\Home;

use MagedIn\Lab\Helper\DockerLab\DirList;
use Symfony\Component\Filesystem\Filesystem;

class Initializer
{
    /**
     * @var DirList
     */
    private DirList $dirList;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param DirList $dirList
     * @param Filesystem $filesystem
     */
    public function __construct(
        DirList $dirList,
        Filesystem $filesystem
    ) {
        $this->dirList = $dirList;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $this->dirList->getMagelabHomeUserDir();
    }
}

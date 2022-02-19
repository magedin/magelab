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

class Installation
{
    /**
     * @return bool
     */
    public function isInstalled(): bool
    {
        return BasePath::isValid();
    }
}

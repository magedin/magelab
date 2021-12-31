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

namespace MagedIn\Lab\Helper\Container;

class PhpInfo
{
    const PHP_DIR = '/usr/local/etc/php';
    const INI_DIR = self::PHP_DIR . DS . 'conf.d';

    /**
     * @return string
     */
    public function getPhpDir(): string
    {
        return self::PHP_DIR;
    }

    /**
     * @return string
     */
    public function getIniDir(): string
    {
        return self::INI_DIR;
    }
}

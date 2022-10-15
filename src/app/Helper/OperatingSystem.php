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

namespace MagedIn\Lab\Helper;

use MagedIn\Lab\Model\Process;

class OperatingSystem
{
    const UNKNOWN_OS = 'Unknown';
    const LINUX_OS = 'Linux';
    const MAC_OS = 'Darwin';

    /**
     * @return string
     */
    public function getName(): string
    {
        $operatingSystem = PHP_OS;
        if ($operatingSystem) {
            return $operatingSystem;
        }
        return self::UNKNOWN_OS;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $operatingSystem = PHP_OS;
        if ($operatingSystem) {
            return $operatingSystem;
        }
        return self::UNKNOWN_OS;
    }

    /**
     * @return bool
     */
    public function isMacOs(): bool
    {
        return $this->getName() === self::MAC_OS;
    }

    /**
     * @return bool
     */
    public function isLinux(): bool
    {
        return $this->getName() === self::LINUX_OS;
    }

    /**
     * @return bool
     */
    public function isWindows(): bool
    {
        if (strtoupper(substr($this->getName(), 0, 3)) === 'WIN') {
            return true;
        }
        return false;
    }
}

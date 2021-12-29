<?php

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
        $operatingSystem = trim(Process::run(['uname'])->getOutput());

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
        $operatingSystem = trim(Process::run(['uname', '-a'])->getOutput());

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
}

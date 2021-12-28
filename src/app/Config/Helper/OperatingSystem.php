<?php

declare(strict_types=1);

namespace MageLab\Config\Helper;

use Symfony\Component\Process\Process;

class OperatingSystem
{
    /**
     * @return string
     */
    public function getName(): string
    {
        $operatingSystem = trim($this->buildProcess(['uname'])->getOutput());

        if ($operatingSystem) {
            return $operatingSystem;
        }

        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $operatingSystem = trim($this->buildProcess(['uname', '-a'])->getOutput());

        if ($operatingSystem) {
            return $operatingSystem;
        }

        return 'Unknown';
    }

    /**
     * @return bool
     */
    public function isMacOs(): bool
    {
        return $this->getName() === 'Darwin';
    }

    /**
     * @return bool
     */
    public function isLinux(): bool
    {
        return $this->getName() === 'Linux';
    }

    /**
     * @param array $arguments
     * @return Process
     */
    private function buildProcess(array $arguments): Process
    {
        $process = new Process($arguments);
        $process->run();
        return $process;
    }
}

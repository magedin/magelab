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

namespace MagedIn\Lab\Model;

use MagedIn\Lab\ObjectManager;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @param array $command
     * @param string|null $cwd
     * @param array|null $env
     * @param $input
     * @param float|null $timeout
     * @return Process
     */
    public function create(
        array $command, string $cwd = null, array $env = null, $input = null, ?float $timeout = 60
    ): Process {
        return ObjectManager::getInstance()->create(Process::class, [
            'command' => $command,
            'cwd' => $cwd,
            'env' => $env,
            'input' => $input,
            'timeout' => $timeout,
        ]);
    }
}

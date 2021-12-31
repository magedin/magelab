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
use Symfony\Component\Process\Process as ComponentProcess;

class Process
{
    /**
     * @var array
     */
    private static array $defaultOptions = [
        'callback' => null,
        'tty'      => false,
        'pty'      => false,
        'env'      => [],
        'cwd'      => null,
        'input'    => null,
        'timeout'  => 60,
    ];

    /**
     * @param array $command
     * @param array $options
     * @return ComponentProcess
     */
    public static function run(array $command, array $options = []): ComponentProcess
    {
        /** @var ProcessFactory $processFactory */
        $processFactory = ObjectManager::getInstance()->create(ProcessFactory::class);
        $opt = array_merge(self::$defaultOptions, $options);

        $process = $processFactory->create($command, $opt['cwd'], $opt['env'], $opt['input'], $opt['timeout']);
        $process->setTty($opt['tty']);
        $process->setPty($opt['pty']);
        $process->run($opt['callback'], $opt['env']);

        return $process;
    }
}

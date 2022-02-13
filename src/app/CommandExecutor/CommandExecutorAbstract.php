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

namespace MagedIn\Lab\CommandExecutor;

use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use MagedIn\Lab\ObjectManager;

abstract class CommandExecutorAbstract implements CommandExecutorInterface
{
    /**
     * @var array|array[]
     */
    protected array $config = [];

    /**
     * @inheritDoc
     */
    public function execute(array $commands = [], array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->doExecute($commands, $config);
    }

    /**
     * @return mixed
     */
    abstract protected function doExecute(array $commands = [], array $config = []);

    /**
     * @param string $key
     * @return array|mixed|null
     */
    protected function getConfig(string $key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return null;
    }

    /**
     * @return array
     */
    protected function getShiftedArgv(): array
    {
        $nonDefaultOptions = ObjectManager::getInstance()->get(NonDefaultOptions::class);
        $argv = $nonDefaultOptions->getArgsV();
        array_shift($argv);
        return $argv;
    }
}

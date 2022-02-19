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

namespace MagedIn\Lab\CommandExecutor\Project;

use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;

class NameExecutor extends CommandExecutorAbstract
{
    /**
     * @var Writer
     */
    private Writer $localConfigWriter;

    public function __construct(
        Writer $localConfigWriter
    ) {
        $this->localConfigWriter = $localConfigWriter;
    }

    protected function doExecute(array $commands = [], array $config = [])
    {
        $localConfig = ['project' => ['name' => $config['name']]];
        $this->localConfigWriter->write($localConfig);;
    }
}

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

namespace MagedIn\Lab\CommandBuilder;

use MagedIn\Lab\Helper\DockerLab\DockerCompose\DockerComposeFilesCollector;
use MagedIn\Lab\Helper\DockerLab\DirList;

class DockerCompose implements CommandBuilderInterface
{
    /**
     * @var DockerComposeFilesCollector
     */
    private DockerComposeFilesCollector $dockerComposeFilesCollector;

    /**
     * @var DirList
     */
    private DirList $dirList;

    public function __construct(
        DockerComposeFilesCollector $dockerComposeFilesCollector,
        DirList $dirList
    ) {
        $this->dockerComposeFilesCollector = $dockerComposeFilesCollector;
        $this->dirList = $dirList;
    }

    /**
     * @var array
     */
    private array $command = [];

    /**
     * @inheritDoc
     */
    public function build(array $subcommands = [], array $options = []): array
    {
        if (!$this->command) {
            $this->buildCommand();
        }
        return array_merge($this->command, $options, $subcommands);
    }

    /**
     * @return void
     */
    private function buildCommand(): void
    {
        $this->command = ['docker-compose'];

        $command = &$this->command;
        array_map(function ($file) use (&$command) {
            $this->command[] = '-f';
            $this->command[] = $file;
        }, $this->dockerComposeFilesCollector->load());
    }
}

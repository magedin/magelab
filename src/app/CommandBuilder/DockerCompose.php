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

use MagedIn\Lab\CommandBuilder\DockerCompose\DockerComposeFiles;
use MagedIn\Lab\Helper\DockerLab\BasePath;

class DockerCompose implements CommandBuilderInterface
{
    /**
     * @var DockerComposeFiles
     */
    private DockerComposeFiles $dockerComposeFiles;

    public function __construct(
        DockerComposeFiles $dockerComposeFiles
    ) {
        $this->dockerComposeFiles = $dockerComposeFiles;
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
        $rootDir = BasePath::getAbsoluteRootDir();
        $this->command = ['docker-compose'];

        $command = &$this->command;
        array_map(function ($file) use ($rootDir, &$command) {
            $this->command[] = '-f';
            $this->command[] = $rootDir . DIRECTORY_SEPARATOR . $file;
        }, $this->dockerComposeFiles->load());
    }
}

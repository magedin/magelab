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

namespace MagedIn\Lab\Helper;

use MagedIn\Lab\CommandBuilder\DockerCompose;
use MagedIn\Lab\Model\Process;

class DockerComposeServicesList
{
    /**
     * @var DockerCompose
     */
    private DockerCompose $dockerComposeCommandBuilder;

    public function __construct(
        DockerCompose $dockerComposeCommandBuilder
    ) {
        $this->dockerComposeCommandBuilder = $dockerComposeCommandBuilder;
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return $this->run(['--services', '--all']);
    }

    /**
     * @param string $service
     * @return string
     */
    public function getId(string $service): string
    {
        $serviceId = $this->getIds([$service]);
        return array_pop($serviceId);
    }

    /**
     * @param array $services
     * @return array
     */
    public function getIds(array $services = []): array
    {
        return $this->run(array_merge(['--quiet', '--all'], $services));
    }

    /**
     * @param array $services
     * @return array
     */
    public function getAll(array $services = []): array
    {
        $services = $this->run(array_merge(['--all'], $services));
        /** Remove the Header and Separator lines. */
        array_shift($services);
        array_shift($services);
        return $services;
    }

    /**
     * @param array $options
     * @return array
     */
    private function run(array $options = []): array
    {
        $options = array_filter($options);
        $command = $this->dockerComposeCommandBuilder->build(array_merge(['ps'], $options));
        $process = Process::run($command);
        $services = explode(PHP_EOL, $process->getOutput());
        return array_filter($services);
    }
}

<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

namespace MagedIn\Lab\CommandExecutor\Container;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\Docker;
use MagedIn\Lab\CommandExecutor\CommandExecutorAbstract;
use MagedIn\Lab\Helper\DockerComposeServicesList;
use MagedIn\Lab\Model\Process;

class Copy extends CommandExecutorAbstract
{
    private Docker $dockerCommandBuilder;
    private DockerComposeServicesList $dockerComposeServicesList;

    public function __construct(
        Docker $dockerCommandBuilder,
        DockerComposeServicesList $dockerComposeServicesList
    ) {
        $this->dockerCommandBuilder = $dockerCommandBuilder;
        $this->dockerComposeServicesList = $dockerComposeServicesList;
    }

    /**
     * @param array $commands
     * @param array $config
     * @return int|mixed
     */
    protected function doExecute(array $commands = [], array $config = [])
    {
        $origin = $config['origin'] ?? '';
        $destination = $config['destination'] ?? '';
        $output = $config['output'] ?? null;

        $subcommand = $this->buildSubcommand($origin, $destination);
        if (empty($subcommand)) {
            return Command::FAILURE;
        }

        $command = $this->dockerCommandBuilder->build($subcommand);
        $options = [
            'tty' => true,
        ];
        if ($output) {
            $options['callback'] = function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            };
        }
        $process = Process::run($command, $options);
        return Command::SUCCESS;
    }

    /**
     * @param string $origin
     * @param string $destination
     * @return array
     */
    private function buildSubcommand(string $origin, string $destination): array
    {
        $separator = ':';

        if (strpos($origin, $separator)) {
            list($service, $path) = explode($separator, $origin);
            $serviceId = $this->findServiceId($service);
            $origin = "$serviceId:$path";
        }

        if (strpos($destination, $separator)) {
            list($service, $path) = explode($separator, $destination);
            $serviceId = $this->findServiceId($service);
            $destination = "$serviceId:$path";
        }

        return ['cp', $origin, $destination];
    }

    /**
     * @param string $service
     * @return string
     */
    private function findServiceId(string $service): string
    {
        return $this->dockerComposeServicesList->getId($service);
    }
}

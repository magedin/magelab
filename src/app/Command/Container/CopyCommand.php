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

namespace MagedIn\Lab\Command\Container;

use MagedIn\Lab\CommandBuilder\Docker;
use MagedIn\Lab\Helper\DockerComposeServicesList;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CopyCommand extends Command
{
    /**
     * @var Docker
     */
    private Docker $dockerCommandBuilder;

    /**
     * @var DockerComposeServicesList
     */
    private DockerComposeServicesList $dockerComposeServicesList;

    public function __construct(
        Docker $dockerCommandBuilder,
        DockerComposeServicesList $dockerComposeServicesList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerCommandBuilder = $dockerCommandBuilder;
        $this->dockerComposeServicesList = $dockerComposeServicesList;
    }

    protected function configure()
    {
        $this->addArgument(
            'origin',
            InputArgument::REQUIRED,
            'The origin file or directory you want to copy. (E.g. /web/somefile.txt | php:/var/www/html)'
        )->addArgument(
            'destination',
            InputArgument::REQUIRED,
            'The destination of your file or directory. (E.g. /web/somefile.txt | php:/var/www/html)'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $origin = $input->getArgument('origin');
        $destination = $input->getArgument('destination');
        $subcommand = $this->buildSubcommand($origin, $destination);

        if (empty($subcommand)) {
            return Command::FAILURE;
        }

        $command = $this->dockerCommandBuilder->build($subcommand);
        $process = Process::run($command, [
            'tty' => true,
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            },
        ]);
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

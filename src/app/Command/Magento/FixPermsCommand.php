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

namespace MagedIn\Lab\Command\Magento;

use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixPermsCommand extends Command
{
    /**
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExecCommandBuilder;

    public function __construct(
        DockerComposeExec $dockerComposeExecCommandBuilder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposeExecCommandBuilder = $dockerComposeExecCommandBuilder;
    }

    protected function configure()
    {
//        $this->addArgument(
//            'subdirectory',
//            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
//            'A subdirectory (Eg: vendor, generated, pub, etc).'
//        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getFileTypesAndPermissions() as $fileType => $filePerm) {
            $mainCommand = ['-type', $fileType, '-exec', 'chmod', $filePerm, '{}', ' \;'];
            $subcommand = array_merge(['php', 'find'], $this->getMagentoDirs(), $mainCommand);
            $rootNoTtyOptions = ['root' => true];
            $command = $this->dockerComposeExecCommandBuilder->build($subcommand, $rootNoTtyOptions);
            Process::run($command, [
                'tty' => true,
                'timeout' => null,
                'callback' => function ($type, $buffer) use ($output) {
                    $output->writeln($buffer);
                },
            ]);
        }
        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function getFileTypesAndPermissions(): array
    {
        return [
            'f' => 'u+w',
            'd' => 'u+w',
        ];
    }

    /**
     * @return string[]
     */
    private function getMagentoDirs(): array
    {
        return ['var', 'vendor', 'pub/static', 'pub/media', 'app/etc'];
    }
}

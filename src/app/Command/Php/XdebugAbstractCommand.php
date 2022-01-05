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

namespace MagedIn\Lab\Command\Php;

use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Helper\Container\Php\XdebugInfo;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class XdebugAbstractCommand extends Command
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExecCommandBuilder;

    /**
     * @var XdebugInfo
     */
    protected XdebugInfo $xdebugInfo;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder,
        XdebugInfo $xdebugInfo,
        string $name = null
    ) {
        parent::__construct($name);
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
        $this->xdebugInfo = $xdebugInfo;
    }

    protected function configure()
    {
        $this->addOption(
            'skip-checks',
            null,
            InputOption::VALUE_NONE,
            'Skip checks before trying to change Xdebug status'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $skipChecks = $this->skipChecks($input);
        if (!$skipChecks) {
            $returnCode = $this->checkXdebugStatus($output);
        }

        if (!$skipChecks && $returnCode === $this->getCheckCode()) {
            $this->writeCheckResult($output);
            return Command::SUCCESS;
        }

        $command = $this->dockerComposePhpExecCommandBuilder->build();
        $command[] = 'sed';
        $command[] = '-i';
        $command[] = '-e';
        $command[] = $this->getSedPattern($input);
        $command[] = $this->getIniFilename();

        Process::run($command, ['tty' => true]);
        $this->writeEndResult($input, $output);
        $this->restartServices($output);
        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function restartServices(OutputInterface $output)
    {
        $xdebugCommand = $this->getApplication()->find('restart');
        $emptyInput = ObjectManager::getInstance()->create(ArrayInput::class, [
            'parameters' => [
                'services' => ['php'],
            ]
        ]);
        $xdebugCommand->run($emptyInput, $output);
    }

    /**
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function checkXdebugStatus(OutputInterface $output)
    {
        $xdebugCommand = $this->getApplication()->find('xdebug:status');
        $emptyInput = ObjectManager::getInstance()->create(ArrayInput::class, [
            'parameters' => ['--silent' => true]
        ]);

        return $xdebugCommand->run($emptyInput, $output);
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    protected function skipChecks(InputInterface $input): bool
    {
        return (bool) $input->getOption('skip-checks');
    }
}

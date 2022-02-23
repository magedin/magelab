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

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerComposeExec;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\Console\Input\ArrayInputFactory;
use MagedIn\Lab\Helper\Container\Php\XdebugInfo;
use MagedIn\Lab\Model\Process;
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
     * @var DockerComposeExec
     */
    private DockerComposeExec $dockerComposeExec;

    /**
     * @var XdebugInfo
     */
    protected XdebugInfo $xdebugInfo;

    /**
     * @var ArrayInputFactory
     */
    protected ArrayInputFactory $arrayInputFactory;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExecCommandBuilder,
        DockerComposeExec $dockerComposeExec,
        XdebugInfo $xdebugInfo,
        ArrayInputFactory $arrayInputFactory,
        string $name = null
    ) {
        $this->dockerComposePhpExecCommandBuilder = $dockerComposePhpExecCommandBuilder;
        $this->dockerComposeExec = $dockerComposeExec;
        $this->xdebugInfo = $xdebugInfo;
        $this->arrayInputFactory = $arrayInputFactory;
        parent::__construct($name);
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

        $subcommand[] = 'sed';
        $subcommand[] = '-i';
        $subcommand[] = '-e';
        $subcommand[] = $this->getSedPattern($input);
        $subcommand[] = $this->getIniFilename();

        $command = $this->dockerComposePhpExecCommandBuilder->build($subcommand);
        Process::run($command, ['tty' => true]);
        $this->writeEndResult($input, $output);
        $this->reloadServices($output);
        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function reloadServices(OutputInterface $output)
    {
        $output->writeInfo('Reloading PHP-FPM service...', true);
        $reloadCommand = $this->dockerComposeExec->build(['php', 'kill', '-USR2', '1']);
        Process::run($reloadCommand, ['pty' => true]);
        $output->writeInfo('PHP-FPM services has been reloaded!', true);
    }

    /**
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function checkXdebugStatus(OutputInterface $output)
    {
        $xdebugCommand = $this->getApplication()->find('xdebug:status');
        $input = $this->arrayInputFactory->create(['--silent' => true]);
        return $xdebugCommand->run($input, $output);
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

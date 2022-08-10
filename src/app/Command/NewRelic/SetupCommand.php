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

namespace MagedIn\Lab\Command\NewRelic;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\CommandBuilder\DockerComposePhpExec;
use MagedIn\Lab\CommandExecutor\Php\PhpFpmReload;
use MagedIn\Lab\Helper\DockerLab\EnvLoader;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    /**
     * @var DockerComposePhpExec
     */
    private DockerComposePhpExec $dockerComposePhpExec;

    /**
     * @var EnvLoader
     */
    private EnvLoader $envLoader;

    /**
     * @var PhpFpmReload
     */
    private PhpFpmReload $phpFpmReload;

    public function __construct(
        DockerComposePhpExec $dockerComposePhpExec,
        EnvLoader $envLoader,
        PhpFpmReload $phpFpmReload,
        string $name = null
    ) {
        $this->dockerComposePhpExec = $dockerComposePhpExec;
        $this->envLoader = $envLoader;
        $this->phpFpmReload = $phpFpmReload;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument(
            'key',
            InputArgument::OPTIONAL,
            'The NewRelic key.'
        );

        $this->addOption(
            'app-name',
            'a',
            InputOption::VALUE_OPTIONAL,
            'The NewRelic app name'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = '/usr/local/etc/php/conf.d/newrelic.ini';
        $existCommand = $this->dockerComposePhpExec->build(['test', '-e', $file], ['root' => true]);
        $process = Process::run($existCommand, ['pty' => true]);
        $reloadFpm = false;

        /** If New Relic is not installed. */
        if ($process->getExitCode() === 1) {
            $installedKey = trim((string) getenv('NR_INSTALL_KEY'));
            $newKey = trim((string) $input->getArgument('key'));

            if (!$installedKey && !$newKey) {
                throw new InvalidArgumentException(
                    "Since it's the first installation you need to provide a valid NewRelic key."
                );
            }

            if (!$installedKey || ($newKey && $installedKey !== $newKey)) {
                $this->validateNewRelicKey($newKey);
                $this->updateKey($newKey);
            }

            $command = $this->dockerComposePhpExec->build(['newrelic-install', 'install'], ['root' => true]);
            $process = Process::run($command, ['pty' => true]);
            if ($process->getOutput()) {
                /** On silent install, NewRelic doesn't return any message. */
                throw new RuntimeException($process->getOutput());
            }

            $reloadFpm = true;
            $output->writelnInfo("NewRelic is now installed and running.");
        } else {
            $output->writelnInfo("NewRelic is already installed and running.");
        }

        if ($appName = $input->getOption('app-name')) {
            $pattern = 's/newrelic.appname.*/newrelic.appname = \"' . $appName . '\"/g';
            $command = $this->dockerComposePhpExec->build(['sed', '-i', '-e', $pattern, $file]);
            Process::run($command, ['pty' => true]);
            $output->writelnInfo("NewRelic app name was updated to '$appName'");
            $reloadFpm = true;
        }

        if (true === $reloadFpm) {
            $this->reloadFpm($output);
        }

        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    private function reloadFpm(OutputInterface $output): void
    {
        $output->writelnInfo("Reloading PHP-FPM service...");
        $this->phpFpmReload->execute();
    }

    /**
     * @param string $key
     * @return bool
     */
    private function validateNewRelicKey(string $key): bool
    {
        if (strlen($key) !== 40) {
            throw new InvalidArgumentException('The NewRelic key provided is not valid. It should 40 characters.');
        }
        return true;
    }

    /**
     * @param string $key
     * @return void
     */
    private function updateKey(string $key): void
    {
        $pattern = "s/NR_INSTALL_KEY\=.*/NR_INSTALL_KEY\=$key/g";
        $command = ['sed', '-i', '-e', $pattern, $this->envLoader->getFileLocation()];
        Process::run($command);
    }
}

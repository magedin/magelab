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

namespace MagedIn\Lab\Command\Mkcert;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\Model\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MkcertSslGenerateCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected bool $isPrivate = false;

    protected function configure()
    {
        $this->addArgument(
            'domains',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'The domains you want to generate the SSL files to.'
        );

        $this->addOption(
            'cert-file',
            null,
            InputOption::VALUE_OPTIONAL,
            'Customize the certificate path.'
        );

        $this->addOption(
            'key-file',
            null,
            InputOption::VALUE_OPTIONAL,
            'Customize the certificate key path.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domains = $input->getArgument('domains');

        $command = ['mkcert'];
        if ($certFile = $input->getOption('cert-file')) {
            $command[] = '-cert-file';
            $command[] = $certFile;
        }

        if ($keyFile = $input->getOption('key-file')) {
            $command[] = '-key-file';
            $command[] = $keyFile;
        }

        $command = array_merge($command, $domains);
        Process::run($command, ['pty' => true]);
        return Command::SUCCESS;
    }
}

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

use MagedIn\Lab\Helper\OperatingSystem;
use MagedIn\Lab\Model\Process;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MkcertSslGenerateCommand extends Command
{
    /**
     * @var OperatingSystem
     */
    private OperatingSystem $operatingSystem;

    public function __construct(
        OperatingSystem $operatingSystem,
        string $name = null
    ) {
        parent::__construct($name);
        $this->operatingSystem = $operatingSystem;
    }

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

        if ($this->operatingSystem->isMacOs()) {
            $executable = BIN_DIR . DS . 'mkcert-darwin-amd64';
        } else {
            $executable = BIN_DIR . DS . 'mkcert-linux-amd64';
        }

        $command = [$executable];
        if ($certFile = $input->getOption('cert-file')) {
            $command[] = '-cert-file';
            $command[] = $certFile;
        }

        if ($keyFile = $input->getOption('key-file')) {
            $command[] = '-key-file';
            $command[] = $keyFile;
        }

        $command = array_merge($command, $domains);
        Process::run($command);
        return Command::SUCCESS;
    }
}

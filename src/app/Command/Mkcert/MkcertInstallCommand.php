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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MkcertInstallCommand extends Command
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
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->operatingSystem->isMacOs()) {
            $executable = BIN_DIR . DS . 'mkcert-darwin-amd64';
        } else {
            $executable = BIN_DIR . DS . 'mkcert-linux-amd64';
        }
        $command = [$executable, '-install'];
        Process::run($command, ['tty' => true]);

        return Command::SUCCESS;
    }
}

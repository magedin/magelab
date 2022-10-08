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

use MagedIn\Lab\CommandExecutor\Container\Copy;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CopyCommand extends Command
{
    /**
     * @var Copy
     */
    private Copy $copyCommandExecutor;

    /**
     * @param Copy $copyCommandExecutor
     * @param string|null $name
     */
    public function __construct(
        Copy $copyCommandExecutor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->copyCommandExecutor = $copyCommandExecutor;
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
        $config = [
            'origin' => $origin,
            'destination' => $destination,
            'output' => $output,
        ];
        return $this->copyCommandExecutor->execute([], $config);
    }
}

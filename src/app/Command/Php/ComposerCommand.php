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

use MagedIn\Lab\Command\ProxyCommand;
use MagedIn\Lab\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerCommand extends ProxyCommand
{
    /**
     * @var array
     */
    protected array $protectedOptions = ['--one', '-1'];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var \MagedIn\Lab\CommandExecutor\CommandExecutorInterface $executor */
        $executor = ObjectManager::getInstance()->get(\MagedIn\Lab\CommandExecutor\Php\Composer::class);
        $executor->execute(['protected_options' => $this->getProtectedOptions()]);
        return Command::SUCCESS;
    }
}

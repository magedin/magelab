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

use MagedIn\Lab\CommandExecutor\Magento\FixOwns;
use MagedIn\Lab\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixOwnsCommand extends Command
{
    private FixOwns $fixOwnsCommandExecutor;

    /**
     * @param FixOwns $fixOwnsCommandExecutor
     * @param string|null $name
     */
    public function __construct(
        FixOwns $fixOwnsCommandExecutor,
        string $name = null
    ) {
        parent::__construct($name);
        $this->fixOwnsCommandExecutor = $fixOwnsCommandExecutor;
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
        $config = [
            'callback' => function ($type, $buffer) use ($output) {
                $output->writeln($buffer);
            }
        ];
        return $this->fixOwnsCommandExecutor->execute([], $config);
    }
}

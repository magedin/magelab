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
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileWriter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SwitchVersionCommand extends Command
{
    /**
     * @var CustomFileWriter
     */
    private CustomFileWriter $customFileWriter;

    public function __construct(
        CustomFileWriter $customFileWriter,
        string $name = null
    ) {
        parent::__construct($name);
        $this->customFileWriter = $customFileWriter;
    }

    protected function configure()
    {
        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The PHP version you want to use on this project.',
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $image = 'magedin/magento2-php';
        $version = $input->getArgument('version');
        $config = [
            'services' => [
                'php' => [
                    'image' => "$image:$version"
                ]
            ]
        ];
        $this->customFileWriter->write($config);
        return Command::SUCCESS;
    }
}

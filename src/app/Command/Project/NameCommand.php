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

namespace MagedIn\Lab\Command\Project;

use MagedIn\Lab\Command\Command;
use MagedIn\Lab\Config;
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileManager;
use MagedIn\Lab\Model\Config\LocalConfig\Writer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NameCommand extends Command
{
    /**
     * @var Writer
     */
    private Writer $localConfigWriter;

    /**
     * @var CustomFileManager
     */
    private CustomFileManager $customFileManager;

    public function __construct(
        Writer $localConfigWriter,
        CustomFileManager $customFileManager,
        string $name = null
    ) {
        $this->localConfigWriter = $localConfigWriter;
        $this->customFileManager = $customFileManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Set the project name.'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (empty($name)) {
            $projectName = Config::get('project/name');
            if (empty($projectName)) {
                $output->writelnInfo("You haven't set the project name yet.");
                return Command::SUCCESS;
            }
            $output->writeln("The current project name is the following:");
            $output->writelnInfo($projectName);
            return Command::SUCCESS;
        }

        $name = $this->sanitizeName($name);
        $localConfig = ['project' => ['name' => $name]];
        $this->localConfigWriter->write($localConfig);
        $this->customFileManager->write([], true);
        $output->writelnInfo("You project now is called '$name'");
        return Command::SUCCESS;
    }

    /**
     * @param string $name
     * @return string
     */
    private function sanitizeName(string $name): string
    {
        $name = preg_replace('/[\s]/', '-', $name);
        $name = preg_replace('/[^0-9a-zA-Z\_\-]/', '', $name);
        $name = trim($name, "-_");
        return strtolower($name);
    }
}

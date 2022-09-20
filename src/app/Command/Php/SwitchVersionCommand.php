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
use MagedIn\Lab\Helper\DockerLab\DockerCompose\CustomFileManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SwitchVersionCommand extends Command
{
    /**
     * @var CustomFileManager
     */
    private CustomFileManager $customFileManager;

    /**
     * @var array|string[]
     */
    private array $availableVersions = ['7.2', '7.3', '7.4', '8.0', '8.1'];

    public function __construct(
        CustomFileManager $customFileManager,
        string $name = null
    ) {
        $this->customFileManager = $customFileManager;
        parent::__construct($name);
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
        $version = $input->getArgument('version');

        if (!$this->validateVersion($version)) {
            $output->writelnError(vsprintf(
                'The PHP version %s is not available. Use of of the following: %s',
                [$version, implode(', ', $this->availableVersions)]
            ));
            return Command::FAILURE;
        }

        if ($this->matchVersions($version)) {
            $output->writelnError(vsprintf('Your environment is already using the PHP version %s.', [$version]));
            return Command::FAILURE;
        }

        $config = [
            'services' => [
                'php' => [
                    'image' => "magedin/magento2-php:$version"
                ],
                'php-debug' => [
                    'image' => "magedin/magento2-php-debug:$version"
                ],
            ],
        ];
        $this->customFileManager->write($config, true);
        $output->writelnInfo(sprintf('Your PHP version was switched to %s', $version));
        $output->writelnInfo(sprintf('In order for this change to take effect, please restart your containers.'));
        return Command::SUCCESS;
    }

    /**
     * @param string $version
     * @return bool
     */
    private function validateVersion(string $version): bool
    {
        return in_array($version, $this->availableVersions);
    }

    /**
     * Check if current PHP version is equal to the new PHP version.
     * @param string $newVersion
     * @return bool
     */
    private function matchVersions(string $newVersion): bool
    {
        $currentCustomConfig = $this->customFileManager->loadCurrentContent();
        $phpImage = $currentCustomConfig['services']['php']['image'] ?? null;
        if (!$phpImage || !strpos($phpImage, ':')) {
            return false;
        }
        list($image, $oldVersion) = explode(':', $phpImage);
        return version_compare($newVersion, $oldVersion) === 0;
    }
}

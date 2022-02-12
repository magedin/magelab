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

namespace MagedIn\Lab\Command;

use MagedIn\Lab\Helper\Console\NonDefaultOptions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * We call special commands the commands that need to call other Symfony\Command process.
 * For instance: n98, bin/magento, and composer also uses Symfony\Command as the console application.
 * This causes conflicts between the default options like --help, --version, --quiet, etc.
 * A special command hand the default options over to the subcommand.
 */
abstract class ProxyCommand extends Command
{
    /**
     * @var NonDefaultOptions
     */
    private NonDefaultOptions $nonDefaultOptions;

    /**
     * @var array
     */
    protected array $protectedOptions = [];

    public function __construct(
        NonDefaultOptions $nonDefaultOptions,
        string $name = null
    ) {
        parent::__construct($name);
        $this->nonDefaultOptions = $nonDefaultOptions;
    }

    /**
     * @return array
     */
    protected function getShiftedArgv(): array
    {
        $argv = $this->nonDefaultOptions->getArgsV();
        array_shift($argv);
        return $argv;
    }

    /**
     * @param array $options
     * @return array
     */
    private function cleanOptions(array $options = []): array
    {
        return array_map(function (string $option) {
            return str_replace('-', '', $option);
        }, $options);
    }

    /**
     * @param bool $clean
     * @return array
     */
    protected function getProtectedOptions(bool $clean = false): array
    {
        $options = $this->protectedOptions;
        if (true === $clean) {
            $options = $this->cleanOptions($options);
        }
        return $options;
    }

    /**
     * @return bool
     */
    public function isProxyCommand(): bool
    {
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        return $this->execute($input, $output);
    }
}

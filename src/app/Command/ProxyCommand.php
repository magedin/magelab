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
use Symfony\Component\Console\Input\InputDefinition;

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
     * Adds the non-default arguments, so they can be validated by the command.
     * For special commands, we are going to pass all the options and arguments over to it.
     * @return InputDefinition
     */
    public function getDefinition()
    {
        $options = array_diff($this->nonDefaultOptions->getOptions(), $this->getProtectedOptions(true));
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return parent::getDefinition();
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
}

<?php

declare(strict_types=1);

namespace MagedIn\Lab\Command;

use MagedIn\Lab\Console\NonDefaultOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * We call special commands the commands that need to call other Symfony\Command process.
 * For instance: n98, bin/magento, and composer also uses Symfony\Command as the console application.
 * This causes conflicts between the default options like --help, --version, --quiet, etc.
 * A special command hand the default options over to the subcommand.
 */
abstract class SpecialCommand extends Command
{
    /**
     * @var NonDefaultOptions
     */
    private NonDefaultOptions $nonDefaultOptions;

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
        $options = array_diff($this->nonDefaultOptions->getOptions(), $this->getProtectedOptions());
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
     * @return array
     */
    protected function getProtectedOptions(): array
    {
        return [];
    }
}

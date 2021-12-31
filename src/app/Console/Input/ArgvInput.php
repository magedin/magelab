<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console\Input;

use MagedIn\Lab\Helper\Console\DefaultOptions;
use MagedIn\Lab\Helper\SpecialCommands;
use Symfony\Component\Console\Input\InputDefinition;

class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{
    /**
     * @var SpecialCommands
     */
    private SpecialCommands $specialCommands;

    /**
     * @var DefaultOptions
     */
    private DefaultOptions $defaultOptions;

    public function __construct(
        SpecialCommands $specialCommands,
        DefaultOptions $defaultOptions,
        array $argv = null,
        InputDefinition $definition = null
    ) {
        parent::__construct($argv, $definition);
        $this->specialCommands = $specialCommands;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        /**
         * Only replace the default options if the command is special.
         * @see \MagedIn\Lab\Console\CommandsBuilder::buildCommand
         */
        if ($this->specialCommands->get($this->getFirstArgument())) {
            $specialOptions = $this->defaultOptions->getOptions();
            if (is_array($values)) {
                $result = array_intersect($specialOptions, $values);
                if ($result) {
                    return false;
                }
            }
        }
        return parent::hasParameterOption($values, $onlyParams);
    }
}

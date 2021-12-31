<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console\Input;

use MagedIn\Lab\Helper\SpecialCommands;
use Symfony\Component\Console\Input\InputDefinition;

class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{
    private SpecialCommands $specialCommands;

    public function __construct(
        SpecialCommands $specialCommands,
        array $argv = null,
        InputDefinition $definition = null
    ) {
        parent::__construct($argv, $definition);
        $this->specialCommands = $specialCommands;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        if ($this->specialCommands->get($this->getFirstArgument())) {
            $specials = ['--ansi', '--no-ansi', '--version', '--help', '--no-interaction', '--quite'];
            if (is_array($values)) {
                $result = array_intersect($specials, $values);
                if ($result) {
                    return false;
                }
            }
        }
        return parent::hasParameterOption($values, $onlyParams);
    }
}

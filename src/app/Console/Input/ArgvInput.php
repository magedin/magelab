<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console\Input;

class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{
    /**
     * {@inheritdoc}
     */
    public function hasParameterOption($values, bool $onlyParams = false)
    {
        if (is_array($values)) {
            $version = array_search('--version', $values);
            $help = array_search('--help', $values);
            if (is_int($version) || is_int($help)) {
                return false;
            }
        }
        return parent::hasParameterOption($values, $onlyParams);
    }
}

<?php

declare(strict_types=1);

namespace MagedIn\Lab\Console;

class NonDefaultOptions
{
    /**
     * @var DefaultOptions
     */
    private DefaultOptions $defaultOptions;

    public function __construct(
        DefaultOptions $defaultOptions
    ) {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->getFilteredOptions();
    }

    /**
     * Return only non-default options.
     * @return array
     */
    private function getFilteredOptions(): array
    {
        /** Return only the commands that are not default commands (e.g. version, help, etc.) */
        return array_diff(array_filter($this->getOnlyOptions()), $this->defaultOptions->getOptionNames());
    }

    /**
     * Return only the options. Strip away the parameters.
     * @return array
     */
    private function getOnlyOptions(): array
    {
        return array_map(function (&$option) {
            if (str_starts_with($option, '--')) {
                return substr($option, 2);
            }
            if (str_starts_with($option, '-')) {
                return substr($option, 1);
            }
            return false;
        }, $this->getArgsV());
    }

    /**
     * Get all arguments from input.
     * @return array
     */
    public function getArgsV(): array
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        array_shift($argv);
        return $argv;
    }
}
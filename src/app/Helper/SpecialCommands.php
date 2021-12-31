<?php

declare(strict_types=1);

namespace MagedIn\Lab\Helper;

class SpecialCommands
{
    private array $commands = [];

    /**
     * @param string $name
     * @return $this
     */
    public function add(string $name): self
    {
        $this->commands[$name] = true;
        return $this;
    }

    /**
     * @param array $aliases
     * @return $this
     */
    public function addAliases(array $aliases): self
    {
        foreach ($aliases as $alias) {
            $this->commands[$alias] = true;
        }
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function get($name): bool
    {
        if (isset($this->commands[$name])) {
            return $this->commands[$name];
        }
        return false;
    }
}

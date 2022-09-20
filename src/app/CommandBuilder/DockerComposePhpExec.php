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

namespace MagedIn\Lab\CommandBuilder;

class DockerComposePhpExec extends DockerComposeExec
{
    /**
     * @param array $subcommands
     * @param array $options
     * @return array
     */
    public function build(array $subcommands = [], array $options = []): array
    {
        $container = $this->getExecutionContainer();
        return array_merge(parent::build([$container], $options), $subcommands);
    }

    /**
     * @return string
     */
    private function getExecutionContainer(): string
    {
        $arguments = $_SERVER['argv'] ?? [];
        foreach ($arguments as $key => $argument) {
            if ($this->validateArgument($argument)) {
                unset($_SERVER['argv'][$key]);
                return 'php-debug';
            }
        }
        return 'php';
    }

    /**
     * @param string $argument
     * @return bool
     */
    private function validateArgument(string $argument = ''): bool
    {
        if (strpos($argument, '--debug') !== false) {
            return true;
        }
        if (strpos($argument, '--xdebug') !== false) {
            return true;
        }
        return false;
    }
}

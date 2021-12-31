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

namespace MagedIn\Lab\Helper\Console;

class DefaultOptions
{
    /**
     * @var string[]
     */
    private array $options = [
        '--ansi',
        '--no-ansi',
        '--version',
        '--help',
        '--no-interaction',
        '--quite',
        '--verbose',
    ];

    /**
     * @return array|string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptionNames(): array
    {
        return array_map(function ($option) {
            return substr($option, 2);
        }, $this->options);
    }
}

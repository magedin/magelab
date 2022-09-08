<?php
/**
 * MagedIn Technology
 *
 * @category  MagedIn MageLab
 * @copyright Copyright (c) 2022 MagedIn Technology.
 *
 * @author    Tiago Sampaio <tiago.sampaio@magedin.com>
 */

declare(strict_types=1);

namespace MagedIn\Lab\Model\Config;

class EnvironmentVariables
{
    /**
     * @param string $key
     * @param string|int $default
     * @return string|null
     */
    public function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

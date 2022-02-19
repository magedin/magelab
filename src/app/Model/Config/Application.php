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

use MagedIn\Lab\Config;

class Application
{
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return Config::get('application/name');
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return Config::get('application/version');
    }

    /**
     * @return string|null
     */
    public function getMode(): ?string
    {
        return Config::get('application/version');
    }
}

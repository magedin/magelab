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

class Services
{
    /**
     * @param string $service
     * @return bool
     */
    public function isEnabled(string $service): bool
    {
        return (bool) Config::get("services/$service/enabled");
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        $services = Config::get('services');
        return array_keys($services);
    }

    /**
     * @return array
     */
    public function getEnabledServices(): array
    {
        $services = Config::get('services');
        foreach ($services as $key => $config) {
            $isActive = $config['enabled'] ?? false;
            if (!$isActive) {
                unset($services[$key]);
            }
        }
        return array_keys($services);
    }
}

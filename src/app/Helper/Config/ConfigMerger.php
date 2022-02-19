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

namespace MagedIn\Lab\Helper\Config;

class ConfigMerger
{
    /**
     * @param array $config
     * @param array $toConfig
     * @return void
     */
    public function merge(array $config = [], array &$toConfig = []): void
    {
        foreach ($config as $key => $value) {
            /** @When the index doesn't exist or is not an array. */
            if (!isset($toConfig[$key]) || !is_array($value)) {
                $toConfig[$key] = $value;
                continue;
            }
            $this->merge($value, $toConfig[$key]);
        }
    }
}

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

class ConfigFacade
{
    /**
     * @var Application
     */
    private Application $application;

    /**
     * @var Services
     */
    private Services $services;

    public function __construct(
        Application $application,
        Services $services
    ) {
        $this->application = $application;
        $this->services = $services;
    }

    /**
     * @return Application
     */
    public function application(): Application
    {
        return $this->application;
    }

    /**
     * @return Services
     */
    public function services(): Services
    {
        return $this->services;
    }
}
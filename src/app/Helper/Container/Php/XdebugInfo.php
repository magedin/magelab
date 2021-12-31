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

namespace MagedIn\Lab\Helper\Container\Php;

use MagedIn\Lab\Helper\Container\PhpInfo;

class XdebugInfo
{
    private PhpInfo $phpInfo;

    public function __construct(
        PhpInfo $phpInfo
    ) {
        $this->phpInfo = $phpInfo;
    }

    const ACTIVATE_FILE_NAME = 'docker-php-ext-xdebug.ini';
    const CONFIG_FILE_NAME = 'zzz-xdebug.ini';

    /**
     * @return string
     */
    public function getActivateFilePath(): string
    {
        return $this->phpInfo->getIniDir() . DS . self::ACTIVATE_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getActivateFileName(): string
    {
        return $this->phpInfo->getIniDir() . DS . self::ACTIVATE_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getConfigFilePath(): string
    {
        return $this->phpInfo->getIniDir() . DS . self::CONFIG_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getConfigFileName(): string
    {
        return $this->phpInfo->getIniDir() . DS . self::CONFIG_FILE_NAME;
    }

    /**
     * @return string
     */
    public function getSedEnablePattern(): string
    {
        return 's/^\;zend_extension/zend_extension/g';
    }

    /**
     * @return string
     */
    public function getSedDisablePattern(): string
    {
        return 's/^zend_extension/\;zend_extension/g';
    }

    /**
     * @param string $requestMode
     * @return string
     */
    public function getSedRequestModeChangePattern(string $requestMode): string
    {
        return "s/^xdebug.start_with_request.*/xdebug.start_with_request = $requestMode/g";
    }

    /**
     * @param string $mode
     * @return string
     */
    public function getSedModeChangePattern(string $mode): string
    {
        return "s/^xdebug.mode.*/xdebug.mode = $mode/g";
    }
}
